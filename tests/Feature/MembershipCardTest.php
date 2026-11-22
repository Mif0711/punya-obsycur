<?php

namespace Tests\Feature;

use App\Models\MemberCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creating_a_member_issues_an_active_card(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.cards.store'), [
                'username' => 'dewi',
                'name' => 'Dewi Lestari',
                'password' => 'Perpusa123!',
                'password_confirmation' => 'Perpusa123!',
                'role' => User::ROLE_USER,
                'is_active' => 1,
                'nickname' => 'Dewi',
                'birth_place' => 'Bandung',
                'birth_date' => '2001-05-12',
                'school' => 'SMA Negeri 1 Bandung',
                'phone' => '08123456789',
                'parent_phone' => '08198765432',
                'address' => 'Jl. Melati No. 3, Bandung',
            ])
            ->assertRedirect(route('admin.cards.index'));

        $member = User::query()->where('username', 'dewi')->firstOrFail();

        $this->assertTrue($member->is_active);
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'nickname' => 'Dewi',
            'birth_place' => 'Bandung',
            'school' => 'SMA Negeri 1 Bandung',
            'parent_phone' => '08198765432',
            'address' => 'Jl. Melati No. 3, Bandung',
        ]);
        $this->assertSame('2001-05-12', $member->fresh()->birth_date?->toDateString());
        $this->assertDatabaseHas('member_cards', [
            'user_id' => $member->id,
            'status' => MemberCard::STATUS_ACTIVE,
        ]);
        $this->assertStringStartsWith('PPS-', $member->memberCard->card_number);
    }

    public function test_card_page_shows_card_data_and_contact_fallbacks(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create([
            'name' => 'Dewi Lestari',
            'nickname' => 'Dewi',
            'birth_place' => 'Bandung',
            'birth_date' => '2001-05-12',
            'school' => 'SMA Negeri 1 Bandung',
            'phone' => '08123456789',
            'parent_phone' => '08198765432',
            'address' => 'Jl. Melati No. 3, Bandung',
        ]);
        MemberCard::factory()->create(['user_id' => $member->id]);

        $this->actingAs($admin)
            ->get(route('admin.cards.index'))
            ->assertOk()
            ->assertSee('Dewi Lestari')
            ->assertSee('Dewi')
            ->assertSee('Bandung, 12 May 2001')
            ->assertSee('SMA Negeri 1 Bandung')
            ->assertSee('WhatsApp:')
            ->assertSee('08123456789')
            ->assertSee('Orang tua:')
            ->assertSee('08198765432')
            ->assertSee('Alamat:')
            ->assertSee('Jl. Melati No. 3, Bandung');
    }

    public function test_admin_account_does_not_receive_a_membership_card(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.cards.store'), [
                'username' => 'petugas',
                'name' => 'Petugas Baru',
                'password' => 'Perpusa123!',
                'password_confirmation' => 'Perpusa123!',
                'role' => User::ROLE_ADMIN,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.cards.index'));

        $petugas = User::query()->where('username', 'petugas')->firstOrFail();

        $this->assertDatabaseMissing('member_cards', ['user_id' => $petugas->id]);
    }

    public function test_lost_card_deactivates_the_member_and_replacement_reactivates_them(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $card = MemberCard::factory()->create([
            'user_id' => $member->id,
            'status' => MemberCard::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.cards.lost', $card))
            ->assertRedirect();

        $this->assertFalse($member->fresh()->is_active);
        $this->assertSame(MemberCard::STATUS_LOST, $card->fresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.cards.replace', $card))
            ->assertRedirect();

        $this->assertTrue($member->fresh()->is_active);
        $this->assertSame(MemberCard::STATUS_REPLACED, $card->fresh()->status);

        $activeCards = MemberCard::query()
            ->where('user_id', $member->id)
            ->where('status', MemberCard::STATUS_ACTIVE)
            ->get();

        $this->assertCount(1, $activeCards);
        $this->assertNotSame($card->card_number, $activeCards->first()->card_number);
    }

    public function test_deactivated_member_cannot_log_in_until_the_card_is_replaced(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['password' => 'Perpusa123!']);
        $card = MemberCard::factory()->create([
            'user_id' => $member->id,
            'status' => MemberCard::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)->post(route('admin.cards.lost', $card));
        $this->post(route('logout'));

        $this->post(route('login.store'), [
            'username' => $member->username,
            'password' => 'Perpusa123!',
            'role' => User::ROLE_USER,
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_admin_can_edit_card_details_without_changing_an_active_member_status(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['is_active' => true]);
        $card = MemberCard::factory()->create([
            'user_id' => $member->id,
            'card_number' => 'PPS-2025-ABC123',
            'status' => MemberCard::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.cards.update', $card), [
                'card_number' => 'pps-2026-def456',
                'issued_at' => '2026-01-15',
                'status' => MemberCard::STATUS_ACTIVE,
                'note' => 'Catatan edit admin.',
            ])
            ->assertRedirect(route('admin.cards.index'));

        $card->refresh();

        $this->assertSame('PPS-2026-DEF456', $card->card_number);
        $this->assertSame('2026-01-15', $card->issued_at->toDateString());
        $this->assertSame('Catatan edit admin.', $card->note);
        $this->assertTrue($member->fresh()->is_active);
    }

    public function test_editing_a_card_to_lost_deactivates_the_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['is_active' => true]);
        $card = MemberCard::factory()->create([
            'user_id' => $member->id,
            'status' => MemberCard::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.cards.update', $card), [
                'card_number' => $card->card_number,
                'issued_at' => $card->issued_at->toDateString(),
                'status' => MemberCard::STATUS_LOST,
                'note' => 'Dilaporkan hilang saat edit.',
            ])
            ->assertRedirect(route('admin.cards.index'));

        $this->assertSame(MemberCard::STATUS_LOST, $card->fresh()->status);
        $this->assertFalse($member->fresh()->is_active);
    }

    public function test_editing_a_lost_card_to_active_reactivates_the_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['is_active' => false]);
        $card = MemberCard::factory()->create([
            'user_id' => $member->id,
            'status' => MemberCard::STATUS_LOST,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.cards.update', $card), [
                'card_number' => $card->card_number,
                'issued_at' => $card->issued_at->toDateString(),
                'status' => MemberCard::STATUS_ACTIVE,
                'note' => null,
            ])
            ->assertRedirect(route('admin.cards.index'));

        $this->assertSame(MemberCard::STATUS_ACTIVE, $card->fresh()->status);
        $this->assertTrue($member->fresh()->is_active);
    }

    public function test_card_number_must_be_unique_when_editing(): void
    {
        $admin = User::factory()->admin()->create();
        $firstCard = MemberCard::factory()->create(['status' => MemberCard::STATUS_ACTIVE]);
        $secondCard = MemberCard::factory()->create(['status' => MemberCard::STATUS_ACTIVE]);

        $this->actingAs($admin)
            ->put(route('admin.cards.update', $firstCard), [
                'card_number' => $secondCard->card_number,
                'issued_at' => $firstCard->issued_at->toDateString(),
                'status' => MemberCard::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('card_number');

        $this->assertSame($firstCard->card_number, $firstCard->fresh()->card_number);
    }

    public function test_cannot_activate_an_old_card_when_the_member_has_another_active_card(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['is_active' => true]);
        $oldCard = MemberCard::factory()->create([
            'user_id' => $member->id,
            'card_number' => 'PPS-2024-AAAAAA',
            'status' => MemberCard::STATUS_REPLACED,
        ]);
        MemberCard::factory()->create([
            'user_id' => $member->id,
            'card_number' => 'PPS-2025-BBBBBB',
            'status' => MemberCard::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.cards.update', $oldCard), [
                'card_number' => $oldCard->card_number,
                'issued_at' => $oldCard->issued_at->toDateString(),
                'status' => MemberCard::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('status');

        $this->assertSame(MemberCard::STATUS_REPLACED, $oldCard->fresh()->status);
    }

    public function test_member_cannot_open_or_update_the_card_edit_page(): void
    {
        $member = User::factory()->create();
        $card = MemberCard::factory()->create(['user_id' => $member->id]);

        $this->actingAs($member)
            ->get(route('admin.cards.edit', $card))
            ->assertForbidden();

        $this->actingAs($member)
            ->put(route('admin.cards.update', $card), [
                'card_number' => $card->card_number,
                'issued_at' => $card->issued_at->toDateString(),
                'status' => MemberCard::STATUS_ACTIVE,
            ])
            ->assertForbidden();
    }
}
