<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::query()
            ->with(['genre', 'author', 'publisher'])
            ->when($request->string('q')->toString(), fn ($query, string $q) => $query->whereLike('title', '%'.$q.'%'))
            ->when($request->integer('genre_id'), fn ($query, int $genreId) => $query->where('genre_id', $genreId))
            ->when($request->string('flag')->toString(), function ($query, string $flag): void {
                $column = match ($flag) {
                    'editor' => 'editor_pick',
                    'award' => 'award_winner',
                    'figure' => 'public_figure_pick',
                    'trend' => 'social_trend',
                    default => null,
                };

                if ($column !== null) {
                    $query->where($column, true);
                }
            })
            ->orderByDesc('id');

        return view('admin.books.index', [
            'books' => $query->paginate(12)->withQueryString(),
            'genres' => Genre::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.books.create', [
            'genres' => Genre::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
        ]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['cover', 'themes']);
        $data['themes'] = $request->input('themes', []);

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Book $book): View
    {
        return view('admin.books.show', [
            'book' => $book->load(['genre', 'author', 'publisher']),
            'available' => Book::whereKey($book->id)->available()->exists(),
        ]);
    }

    public function edit(Book $book): View
    {
        return view('admin.books.edit', [
            'book' => $book,
            'genres' => Genre::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->safe()->except(['cover', 'themes']);
        $data['themes'] = $request->input('themes', []);

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()->route('admin.books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Buku dihapus dari katalog.');
    }
}
