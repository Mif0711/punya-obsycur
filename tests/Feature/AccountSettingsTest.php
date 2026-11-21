<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.edit'))->assertRedirect(route('login'));
    }

    public function test_admin_and_member_can_open_account_settings(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Akun');

        $this->actingAs($member)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Akun');
    }

    public function test_avatar_name_opens_an_account_menu_on_both_portals(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('admin-account-menu', false)
            ->assertSee(route('account.edit'), false);

        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('member-account-menu', false)
            ->assertSee(route('account.edit'), false);
    }

    public function test_user_can_change_username_with_current_password(): void
    {
        $member = User::factory()->create([
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ]);

        $this->actingAs($member)->put(route('account.update'), [
            'username' => 'Budi.Baru',
            'current_password' => 'Perpusa123!',
        ])->assertRedirect();

        $this->assertSame('budi.baru', $member->fresh()->username);
    }

    public function test_user_can_change_password_and_login_with_new_credentials(): void
    {
        $member = User::factory()->create([
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ]);

        $this->actingAs($member)->put(route('account.update'), [
            'username' => 'budi',
            'current_password' => 'Perpusa123!',
            'password' => 'Baru12345!',
            'password_confirmation' => 'Baru12345!',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('Baru12345!', $member->fresh()->password));

        $this->post(route('logout'));
        $this->post(route('login.store'), [
            'username' => 'budi',
            'password' => 'Baru12345!',
        ])->assertRedirect(route('member.dashboard'));
    }

    public function test_username_already_used_by_another_user_is_rejected(): void
    {
        User::factory()->create(['username' => 'sinta']);
        $member = User::factory()->create([
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ]);

        $this->actingAs($member)->put(route('account.update'), [
            'username' => 'SINTA',
            'current_password' => 'Perpusa123!',
        ])->assertSessionHasErrors('username');

        $this->assertSame('budi', $member->fresh()->username);
    }

    public function test_wrong_current_password_prevents_changes(): void
    {
        $member = User::factory()->create([
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ]);

        $this->actingAs($member)->put(route('account.update'), [
            'username' => 'budi.baru',
            'current_password' => 'salah',
        ])->assertSessionHasErrors('current_password');

        $this->assertSame('budi', $member->fresh()->username);
    }

    public function test_new_password_requires_a_matching_confirmation(): void
    {
        $member = User::factory()->create([
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ]);

        $this->actingAs($member)->put(route('account.update'), [
            'username' => 'budi',
            'current_password' => 'Perpusa123!',
            'password' => 'Baru12345!',
            'password_confirmation' => 'Bed12345!',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('Perpusa123!', $member->fresh()->password));
    }
}
