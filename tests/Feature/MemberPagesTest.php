<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Genre;
use App\Models\Loan;
use App\Models\MemberCard;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_member_pages_render_for_an_active_member(): void
    {
        $member = User::factory()->create();
        MemberCard::factory()->create(['user_id' => $member->id]);

        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        Loan::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_BORROWED,
            'due_date' => today()->subDays(4),
            'fine_amount' => 5000,
            'fine_paid' => false,
        ]);

        Reservation::factory()->create([
            'user_id' => $member->id,
            'book_id' => Book::factory()->create()->id,
        ]);

        BookRequest::factory()->create([
            'user_id' => $member->id,
            'genre' => $genre->name,
            'status' => BookRequest::STATUS_PENDING,
        ]);

        $this->actingAs($member);

        $this->get(route('member.dashboard'))->assertOk()->assertSee('Halo, '.$member->name);
        $this->get(route('member.books.index'))->assertOk()->assertSee('Katalog Buku');
        $this->get(route('member.books.show', $book))->assertOk()->assertSee($book->title);
        $this->get(route('member.loans.index'))->assertOk()->assertSee('Peminjaman Saya')->assertSee('Terlambat');
        $this->get(route('member.loans.index', ['status' => 'borrowed']))->assertOk();
        $this->get(route('member.reservations.index'))->assertOk()->assertSee('Booking Buku');
        $this->get(route('member.book-requests.index'))->assertOk()->assertSee('Usulan Buku');
        $this->get(route('member.book-requests.create'))
            ->assertOk()
            ->assertSee('Ajukan Buku Baru')
            ->assertSee('name="genre"', false)
            ->assertDontSee('name="request_type"', false)
            ->assertDontSee('name="genre_id"', false);
        $this->get(route('member.recommendations.index'))->assertOk()->assertSee('Rekomendasi untuk Anda');
    }

    public function test_member_can_submit_a_book_request_with_text_details(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->post(route('member.book-requests.store'), [
                'title' => 'Sejarah Nusantara',
                'genre' => 'Sejarah',
                'publisher' => 'Pustaka Jaya',
                'author' => 'A. H. de S.',
                'reason' => 'Koleksi sejarah lokal masih sangat sedikit di perpustakaan.',
            ])
            ->assertRedirect(route('member.book-requests.index'));

        $this->assertDatabaseHas('book_requests', [
            'user_id' => $member->id,
            'title' => 'Sejarah Nusantara',
            'genre' => 'Sejarah',
            'publisher' => 'Pustaka Jaya',
            'author' => 'A. H. de S.',
            'status' => BookRequest::STATUS_PENDING,
        ]);
    }

    public function test_inactive_member_cannot_open_member_pages(): void
    {
        $member = User::factory()->inactive()->create();

        $this->actingAs($member)
            ->get(route('member.dashboard'))
            ->assertForbidden();
    }
}
