<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookRequest;
use App\Models\BookRequestVote;
use App\Models\Genre;
use App\Models\Loan;
use App\Models\MemberCard;
use App\Models\Publisher;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_pages_render_for_an_admin(): void
    {
        Http::fake();

        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $card = MemberCard::factory()->create(['user_id' => $member->id]);

        $genre = Genre::factory()->create();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $book = Book::factory()->create([
            'genre_id' => $genre->id,
            'publisher_id' => $publisher->id,
            'author_id' => $author->id,
        ]);

        Loan::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_BORROWED,
            'due_date' => today()->subDays(3),
        ]);

        Reservation::factory()->create([
            'user_id' => $member->id,
            'book_id' => $book->id,
        ]);

        BookRequest::factory()->create([
            'user_id' => $member->id,
            'genre' => $genre->name,
        ]);

        $this->actingAs($admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.genres.index'))->assertOk()->assertSee('Detail')->assertDontSee('Lihat Buku');
        $this->get(route('admin.genres.show', $genre))->assertOk()->assertSee($genre->name);
        $this->get(route('admin.publishers.index'))->assertOk()->assertSee('Detail')->assertDontSee('Lihat Buku');
        $this->get(route('admin.publishers.show', $publisher))->assertOk();
        $this->get(route('admin.authors.index'))->assertOk()->assertSee('Detail')->assertDontSee('Lihat Buku');
        $this->get(route('admin.authors.show', $author))->assertOk()->assertSee($author->name);
        $this->get(route('admin.books.index'))->assertOk()->assertSee($book->title);
        $this->get(route('admin.books.show', $book))->assertOk()->assertSee($book->title);
        $this->get(route('admin.books.create'))->assertOk();
        $this->get(route('admin.books.edit', $book))->assertOk();
        $this->get(route('admin.loans.index'))->assertOk();
        $this->get(route('admin.loans.create'))->assertOk();
        $this->get(route('admin.requests.index'))->assertOk();
        $this->get(route('admin.book-requests.index'))->assertOk();
        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.users.create'))->assertOk()->assertSee('Pengguna (anggota)');
        $this->get(route('admin.users.edit', $admin))->assertOk()->assertSee('Ubah Pengguna');
        $this->get(route('admin.cards.index'))->assertOk()->assertSee('Ubah');
        $this->get(route('admin.cards.create'))->assertOk();
        $this->get(route('admin.cards.edit', $card))->assertOk()->assertSee('Ubah Kartu Anggota');
        $this->get(route('admin.import-export.index'))->assertOk();
        $this->get(route('admin.settings.index'))->assertOk();
        $this->get('/admin/google-books')->assertNotFound();
    }

    public function test_admin_book_requests_page_only_has_delete_all_action(): void
    {
        $admin = User::factory()->admin()->create();
        BookRequest::factory()->create(['title' => 'Buku Uji']);

        $this->actingAs($admin)
            ->get(route('admin.book-requests.index'))
            ->assertOk()
            ->assertSee('Buku Uji')
            ->assertSee('Hapus Semua')
            ->assertDontSee('Setujui &amp; Buat Data')
            ->assertDontSee('Terapkan')
            ->assertDontSee('Kelola Genre');
    }

    public function test_admin_can_delete_all_book_requests_with_their_votes(): void
    {
        $admin = User::factory()->admin()->create();
        $bookRequest = BookRequest::factory()->create();
        BookRequestVote::factory()->create(['book_request_id' => $bookRequest->id]);

        $this->actingAs($admin)
            ->delete(route('admin.book-requests.destroy-all'))
            ->assertRedirect();

        $this->assertDatabaseMissing('book_requests', ['id' => $bookRequest->id]);
        $this->assertDatabaseMissing('book_request_votes', ['book_request_id' => $bookRequest->id]);
    }

    public function test_admin_can_update_loan_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'fine_per_day' => 7000,
                'grace_days' => 3,
                'loan_days' => 14,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'fine_per_day', 'value' => '7000']);
        $this->assertDatabaseHas('settings', ['key' => 'grace_days', 'value' => '3']);
        $this->assertDatabaseHas('settings', ['key' => 'loan_days', 'value' => '14']);
    }
}
