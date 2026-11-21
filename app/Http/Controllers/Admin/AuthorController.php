<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.catalog.index', [
            'title' => 'Pengarang',
            'resource' => 'authors',
            'label' => 'Pengarang',
            'items' => Author::query()
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
            'title' => 'Tambah Pengarang',
            'resource' => 'authors',
            'label' => 'Pengarang',
        ]);
    }

    public function store(StoreAuthorRequest $request): RedirectResponse
    {
        Author::create($request->validated());

        return redirect()->route('admin.authors.index')->with('success', 'Pengarang berhasil ditambahkan.');
    }

    public function show(Author $author): View
    {
        return view('admin.catalog.show', [
            'title' => $author->name,
            'resource' => 'authors',
            'label' => 'Pengarang',
            'item' => $author,
            'books' => Book::query()
                ->with(['genre', 'publisher', 'author'])
                ->whereBelongsTo($author)
                ->orderBy('title')
                ->paginate(12),
        ]);
    }

    public function edit(Author $author): View
    {
        return view('admin.catalog.edit', [
            'title' => 'Ubah Pengarang',
            'resource' => 'authors',
            'label' => 'Pengarang',
            'item' => $author,
        ]);
    }

    public function update(StoreAuthorRequest $request, Author $author): RedirectResponse
    {
        $author->update($request->validated());

        return redirect()->route('admin.authors.index')->with('success', 'Pengarang berhasil diperbarui.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        $author->delete();

        return redirect()->route('admin.authors.index')->with('success', 'Pengarang dihapus.');
    }
}
