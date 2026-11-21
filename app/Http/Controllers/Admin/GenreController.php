<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenreController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.catalog.index', [
            'title' => 'Genre Buku',
            'resource' => 'genres',
            'label' => 'Genre',
            'items' => Genre::query()
                ->withCount('books')
                ->when($request->string('q')->toString(), fn ($query, string $q) => $query->whereLike('name', '%'.$q.'%'))
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.catalog.create', [
            'title' => 'Tambah Genre',
            'resource' => 'genres',
            'label' => 'Genre',
        ]);
    }

    public function store(StoreGenreRequest $request): RedirectResponse
    {
        Genre::create($request->validated());

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil ditambahkan.');
    }

    public function show(Genre $genre): View
    {
        return view('admin.catalog.show', [
            'title' => $genre->name,
            'resource' => 'genres',
            'label' => 'Genre',
            'item' => $genre,
            'books' => Book::query()
                ->with(['author', 'publisher'])
                ->whereBelongsTo($genre)
                ->orderBy('title')
                ->paginate(12),
        ]);
    }

    public function edit(Genre $genre): View
    {
        return view('admin.catalog.edit', [
            'title' => 'Ubah Genre',
            'resource' => 'genres',
            'label' => 'Genre',
            'item' => $genre,
        ]);
    }

    public function update(StoreGenreRequest $request, Genre $genre): RedirectResponse
    {
        $genre->update($request->validated());

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $genre->delete();

        return redirect()->route('admin.genres.index')->with('success', 'Genre dihapus.');
    }
}
