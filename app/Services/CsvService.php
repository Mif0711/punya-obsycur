<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvService
{
    public const TYPES = ['books', 'users'];

    /**
     * @return array<int, string>
     */
    public function headers(string $type): array
    {
        return match ($type) {
            'books' => ['title', 'genre', 'publisher', 'author', 'isbn', 'description', 'editor_pick', 'award_winner', 'public_figure_pick', 'social_trend', 'themes'],
            'users' => ['username', 'name', 'role', 'is_active', 'phone', 'address', 'password'],
            default => [],
        };
    }

    public function export(string $type): StreamedResponse
    {
        $headers = $this->headers($type);

        return response()->streamDownload(function () use ($type, $headers): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            if ($type === 'books') {
                Book::query()
                    ->with(['genre', 'author', 'publisher'])
                    ->orderBy('id')
                    ->chunk(200, function ($books) use ($handle): void {
                        foreach ($books as $book) {
                            fputcsv($handle, [
                                $book->title,
                                $book->genre->name,
                                $book->publisher->name,
                                $book->author->name,
                                $book->isbn,
                                $book->description,
                                $book->editor_pick ? 1 : 0,
                                $book->award_winner ? 1 : 0,
                                $book->public_figure_pick ? 1 : 0,
                                $book->social_trend ? 1 : 0,
                                implode('|', $book->themes),
                            ]);
                        }
                    });
            } else {
                User::query()
                    ->orderBy('id')
                    ->chunk(200, function ($users) use ($handle): void {
                        foreach ($users as $user) {
                            fputcsv($handle, [
                                $user->username,
                                $user->name,
                                $user->role,
                                $user->is_active ? 1 : 0,
                                $user->phone,
                                $user->address,
                                '',
                            ]);
                        }
                    });
            }

            fclose($handle);
        }, "perpusa-{$type}-".today()->toDateString().'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{imported: int, skipped: int}
     */
    public function import(string $type, UploadedFile $file, MemberCardService $memberCards): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return ['imported' => 0, 'skipped' => 0];
        }

        $expectedHeaders = $this->headers($type);
        $headers = fgetcsv($handle) ?: [];
        $headers = array_map(fn (string $header): string => trim(ltrim($header, "\xEF\xBB\xBF")), $headers);

        if ($headers !== $expectedHeaders) {
            fclose($handle);

            return ['imported' => 0, 'skipped' => 0];
        }

        $imported = 0;
        $skipped = 0;
        $columns = $expectedHeaders;

        DB::transaction(function () use ($handle, $type, $columns, $memberCards, &$imported, &$skipped): void {
            while (($row = fgetcsv($handle)) !== false) {
                if ($row === [] || (count($row) === 1 && trim((string) $row[0]) === '')) {
                    continue;
                }

                $row = array_pad(array_slice($row, 0, count($columns)), count($columns), null);
                $data = array_combine($columns, $row);

                try {
                    if ($type === 'books') {
                        $this->importBook($data);
                    } else {
                        $user = $this->importUser($data);
                        if ($user->wasRecentlyCreated && $user->isMember() && ! $user->memberCard()->exists()) {
                            $memberCards->issue($user);
                        }
                    }

                    $imported++;
                } catch (\Throwable) {
                    $skipped++;
                }
            }
        });

        fclose($handle);

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    /**
     * @param  array<string, string|false|null>  $data
     */
    private function importBook(array $data): void
    {
        $genre = Genre::firstOrCreate(['name' => trim((string) $data['genre'])], ['description' => null]);
        $publisher = Publisher::firstOrCreate(['name' => trim((string) $data['publisher'])], ['description' => null]);
        $author = Author::firstOrCreate(['name' => trim((string) $data['author'])], ['description' => null]);

        Book::updateOrCreate(
            ['isbn' => trim((string) $data['isbn']) ?: null],
            [
                'title' => trim((string) $data['title']),
                'genre_id' => $genre->id,
                'publisher_id' => $publisher->id,
                'author_id' => $author->id,
                'description' => $data['description'] ?: null,
                'editor_pick' => (bool) $data['editor_pick'],
                'award_winner' => (bool) $data['award_winner'],
                'public_figure_pick' => (bool) $data['public_figure_pick'],
                'social_trend' => (bool) $data['social_trend'],
                'themes' => array_values(array_filter(explode('|', (string) $data['themes']))),
            ],
        );
    }

    /**
     * @param  array<string, string|false|null>  $data
     */
    private function importUser(array $data): User
    {
        return User::updateOrCreate(
            ['username' => strtolower(trim((string) $data['username']))],
            [
                'name' => trim((string) $data['name']),
                'role' => $data['role'] === 'admin' ? User::ROLE_ADMIN : User::ROLE_USER,
                'is_active' => (bool) $data['is_active'],
                'phone' => $data['phone'] ?: null,
                'address' => $data['address'] ?: null,
                'password' => $data['password'] ?: 'password',
            ],
        );
    }
}
