<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\User;
use App\Services\FineCalculator;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CirculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_direct_loan_is_auto_approved_with_configured_due_date(): void
    {
        Setting::put(Setting::KEY_LOAN_DAYS, '10');

        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $book = Book::factory()->create();

        $loan = app(LoanService::class)->create($member, $book, true, $admin);

        $this->assertSame(Loan::STATUS_APPROVED, $loan->status);
        $this->assertSame($admin->id, $loan->approved_by);
        $this->assertTrue($loan->due_date->isSameDay(today()->addDays(10)));
    }

    public function test_member_loan_request_is_pending_until_admin_approves_it(): void
    {
        Setting::put(Setting::KEY_LOAN_DAYS, '7');

        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($member)
            ->post(route('member.loans.store'), ['book_id' => $book->id])
            ->assertRedirect(route('member.loans.index'));

        $loan = Loan::query()->sole();
        $this->assertSame(Loan::STATUS_PENDING, $loan->status);
        $this->assertNull($loan->approved_by);

        $this->actingAs($admin)
            ->post(route('admin.requests.loans.approve', $loan))
            ->assertRedirect();

        $loan->refresh();
        $this->assertSame(Loan::STATUS_APPROVED, $loan->status);
        $this->assertSame($admin->id, $loan->approved_by);
        $this->assertTrue($loan->due_date->isSameDay(today()->addDays(7)));
    }

    public function test_admin_can_reject_a_pending_loan_request(): void
    {
        $admin = User::factory()->admin()->create();
        $loan = Loan::factory()->pending()->create();

        $this->actingAs($admin)
            ->post(route('admin.requests.loans.reject', $loan))
            ->assertRedirect();

        $this->assertSame(Loan::STATUS_REJECTED, $loan->fresh()->status);
    }

    public function test_loan_request_is_rejected_when_the_only_copy_is_unavailable(): void
    {
        $member = User::factory()->create();
        $book = Book::factory()->create();
        Loan::factory()->create([
            'book_id' => $book->id,
            'status' => Loan::STATUS_APPROVED,
        ]);

        $this->actingAs($member)
            ->post(route('member.loans.store'), ['book_id' => $book->id])
            ->assertSessionHasErrors(['book_id' => 'Buku sedang tidak tersedia saat ini.']);

        $this->assertSame(1, Loan::query()->count());
    }

    public function test_loan_request_is_rejected_when_the_only_copy_is_reserved(): void
    {
        $member = User::factory()->create();
        $book = Book::factory()->create();
        Reservation::factory()->create([
            'book_id' => $book->id,
            'status' => Reservation::STATUS_APPROVED,
        ]);

        $this->actingAs($member)
            ->post(route('member.loans.store'), ['book_id' => $book->id])
            ->assertSessionHasErrors('book_id');

        $this->assertSame(0, Loan::query()->count());
    }

    public function test_books_table_has_no_stock_column(): void
    {
        $this->assertFalse(Schema::hasColumn('books', 'stock'));
    }

    public function test_fine_ignores_grace_days_and_charges_afterwards(): void
    {
        Setting::put(Setting::KEY_GRACE_DAYS, '2');
        Setting::put(Setting::KEY_FINE_PER_DAY, '5000');

        $calculator = app(FineCalculator::class);

        $withinGrace = Loan::factory()->make([
            'due_date' => today()->subDay(),
            'return_date' => today(),
        ]);

        $this->assertEquals(
            ['days_late' => 0, 'amount' => 0],
            $calculator->calculate($withinGrace),
        );

        $late = Loan::factory()->make([
            'due_date' => today()->subDays(5),
            'return_date' => today(),
        ]);

        $this->assertEquals(
            ['days_late' => 3, 'amount' => 15000],
            $calculator->calculate($late),
        );
    }

    public function test_returning_a_late_book_records_the_fine_and_marks_it_returned(): void
    {
        Setting::put(Setting::KEY_GRACE_DAYS, '2');
        Setting::put(Setting::KEY_FINE_PER_DAY, '5000');

        $loan = Loan::factory()->create([
            'status' => Loan::STATUS_BORROWED,
            'due_date' => today()->subDays(4),
        ]);

        $returned = app(LoanService::class)->returnBook($loan);

        $this->assertSame(Loan::STATUS_RETURNED, $returned->status);
        $this->assertTrue($returned->return_date->isSameDay(today()));
        $this->assertSame('10000.00', $returned->fine_amount);
        $this->assertFalse($returned->fine_paid);
    }

    public function test_member_cannot_return_a_book_through_a_direct_request(): void
    {
        $member = User::factory()->create();
        $loan = Loan::factory()->create([
            'user_id' => $member->id,
            'status' => Loan::STATUS_BORROWED,
            'due_date' => today()->addDays(2),
        ]);

        $this->actingAs($member)
            ->post('/member/loans/'.$loan->id.'/return')
            ->assertNotFound();

        $this->assertSame(Loan::STATUS_BORROWED, $loan->fresh()->status);
    }

    public function test_admin_can_mark_an_outstanding_fine_as_paid(): void
    {
        $admin = User::factory()->admin()->create();
        $loan = Loan::factory()->returned()->create([
            'fine_amount' => 7500,
            'fine_paid' => false,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.loans.pay-fine', $loan))
            ->assertRedirect();

        $this->assertTrue($loan->fresh()->fine_paid);
    }
}
