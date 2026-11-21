<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Loan;
use App\Models\MemberCard;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_badges_for_pending_requests_and_book_requests(): void
    {
        $admin = User::factory()->admin()->create();

        Loan::factory()->create(['status' => Loan::STATUS_PENDING]);
        Reservation::factory()->create(['status' => Reservation::STATUS_PENDING]);
        BookRequest::factory()->create(['status' => BookRequest::STATUS_PENDING]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-nav-badge="admin.requests" data-count="2"', false)
            ->assertSee('data-nav-badge="admin.book-requests" data-count="1"', false);
    }

    public function test_admin_sees_badges_for_lost_cards_and_overdue_loans(): void
    {
        $admin = User::factory()->admin()->create();

        MemberCard::factory()->create(['status' => MemberCard::STATUS_LOST]);
        Loan::factory()->create([
            'status' => Loan::STATUS_BORROWED,
            'due_date' => today()->subDays(3),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-nav-badge="admin.cards" data-count="1"', false)
            ->assertSee('data-nav-badge="admin.loans" data-count="1"', false);
    }

    public function test_admin_badge_clears_after_opening_the_nav(): void
    {
        $admin = User::factory()->admin()->create();

        Loan::factory()->create(['status' => Loan::STATUS_PENDING]);
        Reservation::factory()->create(['status' => Reservation::STATUS_PENDING]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('data-nav-badge="admin.requests" data-count="2"', false);

        $this->actingAs($admin)
            ->get(route('admin.requests.index'))
            ->assertOk()
            ->assertDontSee('data-nav-badge="admin.requests"', false);

        $this->assertDatabaseHas('nav_notifications', [
            'user_id' => $admin->id,
            'nav_key' => 'admin.requests',
        ]);
    }

    public function test_a_new_request_after_visiting_the_nav_shows_the_badge_again(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.requests.index'))->assertOk();

        $this->travel(2)->minutes();

        Loan::factory()->create(['status' => Loan::STATUS_PENDING]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-nav-badge="admin.requests" data-count="1"', false);
    }

    public function test_member_sees_a_badge_only_for_their_own_loan_updates(): void
    {
        $member = User::factory()->create();
        $other = User::factory()->create();

        Loan::factory()->create([
            'user_id' => $member->id,
            'status' => Loan::STATUS_APPROVED,
        ]);
        Loan::factory()->create([
            'user_id' => $other->id,
            'status' => Loan::STATUS_APPROVED,
        ]);

        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('data-nav-badge="member.loans" data-count="1"', false);
    }

    public function test_member_badge_clears_after_opening_the_nav(): void
    {
        $member = User::factory()->create();

        Loan::factory()->create([
            'user_id' => $member->id,
            'status' => Loan::STATUS_APPROVED,
        ]);

        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertSee('data-nav-badge="member.loans" data-count="1"', false);

        $this->actingAs($member)
            ->get(route('member.loans.index'))
            ->assertOk()
            ->assertDontSee('data-nav-badge="member.loans"', false);
    }

    public function test_new_book_shows_a_badge_on_the_catalog_nav(): void
    {
        $member = User::factory()->create();

        Book::factory()->create(['created_at' => now()]);

        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('data-nav-badge="member.books" data-count="1"', false);
    }

    public function test_badges_are_isolated_between_users(): void
    {
        $member = User::factory()->create();
        $other = User::factory()->create();

        Loan::factory()->create([
            'user_id' => $member->id,
            'status' => Loan::STATUS_APPROVED,
        ]);

        $this->actingAs($other)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertDontSee('data-nav-badge="member.loans"', false);
    }
}
