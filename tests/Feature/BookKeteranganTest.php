<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookKeteranganTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_book_keterangan_and_members_can_read_it(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $genre = Genre::factory()->create();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.books.store'), [
                'title' => 'Laut Bercerita',
                'genre_id' => $genre->id,
                'publisher_id' => $publisher->id,
                'author_id' => $author->id,
                'isbn' => '9786024246945',
                'description' => 'Deskripsi lengkap buku.',
                'keterangan' => 'Ringkasan yang tercetak di cover belakang.',
            ])
            ->assertRedirect(route('admin.books.index'));

        $book = Book::query()->where('isbn', '9786024246945')->firstOrFail();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'keterangan' => 'Ringkasan yang tercetak di cover belakang.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.books.show', $book))
            ->assertOk()
            ->assertSee('Ringkasan yang tercetak di cover belakang.');

        $this->actingAs($member)
            ->get(route('member.books.show', $book))
            ->assertOk()
            ->assertSee('Ringkasan yang tercetak di cover belakang.');
    }

    public function test_admin_can_update_book_keterangan(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['keterangan' => 'Keterangan lama.']);

        $this->actingAs($admin)
            ->put(route('admin.books.update', $book), [
                'title' => $book->title,
                'genre_id' => $book->genre_id,
                'publisher_id' => $book->publisher_id,
                'author_id' => $book->author_id,
                'isbn' => $book->isbn,
                'description' => $book->description,
                'keterangan' => 'Keterangan baru dari cover belakang.',
            ])
            ->assertRedirect(route('admin.books.index'));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'keterangan' => 'Keterangan baru dari cover belakang.',
        ]);
    }
}
