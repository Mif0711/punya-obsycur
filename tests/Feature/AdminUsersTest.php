<?php

namespace Tests\Feature;

use App\Models\MemberCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_select_defaults_to_a_member_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Pengguna (anggota)')
            ->assertSee('value="user"', false)
            ->assertSee('<option value="user" selected>', false)
            ->assertDontSee('value="admin" selected', false);
    }

    public function test_admin_can_create_a_member_account_with_an_automatic_card(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'username' => 'dewi',
                'name' => 'Dewi Lestari',
                'password' => 'Perpusa123!',
                'password_confirmation' => 'Perpusa123!',
                'role' => User::ROLE_USER,
                'is_active' => 1,
                'phone' => '08123456789',
                'address' => 'Bandung',
            ])
            ->assertRedirect(route('admin.users.index'));

        $member = User::query()->where('username', 'dewi')->firstOrFail();

        $this->assertTrue($member->isMember());
        $this->assertDatabaseHas('member_cards', [
            'user_id' => $member->id,
            'status' => MemberCard::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_can_create_an_admin_account_without_a_card(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'username' => 'petugas',
                'name' => 'Petugas Baru',
                'password' => 'Perpusa123!',
                'password_confirmation' => 'Perpusa123!',
                'role' => User::ROLE_ADMIN,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.index'));

        $petugas = User::query()->where('username', 'petugas')->firstOrFail();

        $this->assertTrue($petugas->isAdmin());
        $this->assertDatabaseMissing('member_cards', ['user_id' => $petugas->id]);
    }

    public function test_duplicate_username_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['username' => 'budi']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'username' => 'BUDI',
                'name' => 'Budi Duplikat',
                'password' => 'Perpusa123!',
                'password_confirmation' => 'Perpusa123!',
                'role' => User::ROLE_USER,
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('username');

        $this->assertSame(2, User::query()->count());
    }

    public function test_member_cannot_access_user_management(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_update_user_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create([
            'username' => 'budi',
            'name' => 'Budi Santoso',
            'phone' => '081200000002',
            'address' => 'Jl. Melati No. 10',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $member), [
                'username' => 'BUDI.BARU',
                'name' => 'Budi Prasetyo',
                'role' => User::ROLE_USER,
                'phone' => '081399999999',
                'address' => 'Jl. Anggrek No. 7',
                'is_active' => 0,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'username' => 'budi.baru',
            'name' => 'Budi Prasetyo',
            'phone' => '081399999999',
            'address' => 'Jl. Anggrek No. 7',
            'is_active' => false,
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_updating_user_without_a_password_keeps_the_current_password(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['password' => 'Perpusa123!']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $member), [
                'username' => $member->username,
                'name' => 'Nama Baru',
                'role' => User::ROLE_USER,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue(Hash::check('Perpusa123!', $member->fresh()->password));
    }

    public function test_admin_can_update_user_password(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['password' => 'Perpusa123!']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $member), [
                'username' => $member->username,
                'name' => $member->name,
                'role' => User::ROLE_USER,
                'is_active' => 1,
                'password' => 'KataSandiBaru123!',
                'password_confirmation' => 'KataSandiBaru123!',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue(Hash::check('KataSandiBaru123!', $member->fresh()->password));
    }

    public function test_changing_member_to_admin_removes_the_membership_card(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['role' => User::ROLE_USER]);
        MemberCard::factory()->create(['user_id' => $member->id]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $member), [
                'username' => $member->username,
                'name' => $member->name,
                'role' => User::ROLE_ADMIN,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($member->fresh()->isAdmin());
        $this->assertDatabaseMissing('member_cards', ['user_id' => $member->id]);
    }

    public function test_changing_admin_to_member_issues_a_membership_card(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->admin()->create(['username' => 'petugas']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $petugas), [
                'username' => $petugas->username,
                'name' => $petugas->name,
                'role' => User::ROLE_USER,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($petugas->fresh()->isMember());
        $this->assertDatabaseHas('member_cards', [
            'user_id' => $petugas->id,
            'status' => MemberCard::STATUS_ACTIVE,
        ]);
    }

    public function test_duplicate_username_is_rejected_when_editing(): void
    {
        $admin = User::factory()->admin()->create();
        $budi = User::factory()->create(['username' => 'budi']);
        $sinta = User::factory()->create(['username' => 'sinta']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $sinta), [
                'username' => 'BUDI',
                'name' => $sinta->name,
                'role' => User::ROLE_USER,
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('username');

        $this->assertSame('sinta', $sinta->fresh()->username);
        $this->assertSame('budi', $budi->fresh()->username);
    }

    public function test_member_cannot_edit_users(): void
    {
        $member = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($member)
            ->put(route('admin.users.update', $other), [
                'username' => 'diedit',
                'name' => 'Diubah Paksa',
                'role' => User::ROLE_ADMIN,
                'is_active' => 1,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $other->id,
            'username' => $other->username,
            'name' => $other->name,
        ]);
        $this->assertDatabaseMissing('users', ['username' => 'diedit']);
    }
}
