<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\MemberCard;
use App\Models\User;
use App\Services\CsvService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CsvImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_books_from_csv(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->createWithContent('books.csv', implode("\n", [
            'title,genre,publisher,author,isbn,description,editor_pick,award_winner,public_figure_pick,social_trend,themes',
            'Laut Bercerita,Fiksi,Gramedia,Leila S. Chudori,9786024246945,Novel sejarah,1,0,1,0,Sejarah|Keluarga',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.import-export.import'), [
                'type' => 'books',
                'file' => $file,
            ])
            ->assertRedirect(route('admin.import-export.index'));

        $book = Book::query()->where('isbn', '9786024246945')->firstOrFail();

        $this->assertSame('Laut Bercerita', $book->title);
        $this->assertTrue($book->editor_pick);
        $this->assertTrue($book->public_figure_pick);
        $this->assertSame(['Sejarah', 'Keluarga'], $book->themes);
        $this->assertSame('Fiksi', $book->genre->name);
        $this->assertSame('Gramedia', $book->publisher->name);
        $this->assertSame('Leila S. Chudori', $book->author->name);
    }

    public function test_import_pads_short_rows_and_skips_blank_lines(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->createWithContent('books.csv', implode("\n", [
            'title,genre,publisher,author,isbn,description,editor_pick,award_winner,public_figure_pick,social_trend,themes',
            'Bumi Manusia,Fiksi,Gramedia,Pramoedya,9789799731234,,',
            '',
            'Cantik Itu Luka,Fiksi,Gramedia,Eka Kurniawan,9789793062792',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.import-export.import'), [
                'type' => 'books',
                'file' => $file,
            ])
            ->assertRedirect(route('admin.import-export.index'))
            ->assertSessionHas('success', 'Import selesai: 2 baris masuk, 0 baris dilewati.');

        $this->assertSame(2, Book::query()->count());
        $this->assertSame('Bumi Manusia', Book::query()->where('isbn', '9789799731234')->firstOrFail()->title);
        $this->assertSame('Cantik Itu Luka', Book::query()->where('isbn', '9789793062792')->firstOrFail()->title);
    }

    public function test_new_members_imported_from_csv_receive_a_card(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->createWithContent('users.csv', implode("\n", [
            'username,name,role,is_active,phone,address,password',
            'dewi,Dewi Lestari,user,1,08123456789,Bandung,rahasia123',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.import-export.import'), [
                'type' => 'users',
                'file' => $file,
            ])
            ->assertRedirect(route('admin.import-export.index'));

        $member = User::query()->where('username', 'dewi')->firstOrFail();

        $this->assertSame('Dewi Lestari', $member->name);
        $this->assertTrue($member->isMember());
        $this->assertTrue($member->is_active);
        $this->assertTrue($member->memberCard()->exists());
    }

    public function test_import_with_unexpected_headers_is_reported_as_skipped(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->createWithContent('books.csv', implode("\n", [
            'wrong,headers,here',
            'a,b,c',
        ]));

        $this->actingAs($admin)
            ->post(route('admin.import-export.import'), [
                'type' => 'books',
                'file' => $file,
            ])
            ->assertRedirect(route('admin.import-export.index'))
            ->assertSessionHas('success', 'Import selesai: 0 baris masuk, 0 baris dilewati.');

        $this->assertSame(0, Book::query()->count());
    }

    public function test_admin_can_export_books_and_users_as_native_csv(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create();
        MemberCard::factory()->create();
        User::factory()->create();

        $booksResponse = $this->actingAs($admin)
            ->get(route('admin.import-export.export', 'books'));

        $booksResponse->assertOk();
        $this->assertStringStartsWith('title,genre,publisher,author,isbn', $booksResponse->streamedContent());

        $usersResponse = $this->actingAs($admin)
            ->get(route('admin.import-export.export', 'users'));

        $usersResponse->assertOk();
        $this->assertStringStartsWith('username,name,role,is_active', $usersResponse->streamedContent());
    }

    public function test_import_rejects_non_csv_files(): void
    {
        $admin = User::factory()->admin()->create();
        $file = UploadedFile::fake()->createWithContent('users.txt', 'not a csv');

        $this->actingAs($admin)
            ->post(route('admin.import-export.import'), [
                'type' => 'users',
                'file' => $file,
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_member_cannot_import_or_export_data(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->post(route('admin.import-export.import'), ['type' => 'books'])
            ->assertForbidden();

        $this->actingAs($member)
            ->get(route('admin.import-export.export', 'books'))
            ->assertForbidden();
    }

    public function test_csv_service_headers_match_export_columns(): void
    {
        $service = new CsvService;

        $this->assertSame(
            ['title', 'genre', 'publisher', 'author', 'isbn', 'description', 'editor_pick', 'award_winner', 'public_figure_pick', 'social_trend', 'themes'],
            $service->headers('books'),
        );

        $this->assertSame(
            ['username', 'name', 'role', 'is_active', 'phone', 'address', 'password'],
            $service->headers('users'),
        );
    }
}
