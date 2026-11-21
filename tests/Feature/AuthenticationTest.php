<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_login_with_username_and_password(): void
    {
        $member = User::factory()->create([
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ]);

        $this->post(route('login.store'), [
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ])->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticatedAs($member);
    }

    public function test_admin_can_login_with_username_and_password(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin',
            'password' => 'Perpusa123!',
        ]);

        $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'Perpusa123!',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_destination_is_determined_by_the_username_not_a_portal_choice(): void
    {
        $member = User::factory()->create([
            'username' => 'budi',
            'password' => 'Perpusa123!',
        ]);

        $this->post(route('login.store'), [
            'username' => 'budi',
            'password' => 'Perpusa123!',
            'role' => User::ROLE_ADMIN,
        ])->assertRedirect(route('member.dashboard'));

        $this->assertAuthenticatedAs($member);
    }

    public function test_login_page_shows_a_circular_form_without_portal_selector(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('portal-form-orb', false)
            ->assertSee('portal-input-has-icon', false)
            ->assertDontSee('data-role-toggle', false)
            ->assertDontSee('name="role"', false);
    }

    public function test_inactive_account_is_rejected_with_a_clear_message(): void
    {
        User::factory()->inactive()->create([
            'username' => 'rina',
            'password' => 'Perpusa123!',
        ]);

        $this->post(route('login.store'), [
            'username' => 'rina',
            'password' => 'Perpusa123!',
        ])->assertSessionHasErrors([
            'username' => 'Akun tidak aktif. Kartu anggota Anda mungkin hilang; hubungi admin.',
        ]);

        $this->assertGuest();
    }

    public function test_invalid_password_is_rejected(): void
    {
        User::factory()->create(['username' => 'sinta']);

        $this->post(route('login.store'), [
            'username' => 'sinta',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('member.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_roles_cannot_access_each_others_portals(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->actingAs($admin)->get(route('member.dashboard'))->assertForbidden();
        $this->actingAs($member)->get(route('admin.dashboard'))->assertForbidden();
    }
}
