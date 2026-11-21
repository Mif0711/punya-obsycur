<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublisherRequest;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublisherController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.catalog.index', [
            'title' => 'Penerbit',
            'resource' => 'publishers',
            'label' => 'Penerbit',
            'items' => Publisher::query()
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
            'title' => 'Tambah Penerbit',
            'resource' => 'publishers',
            'label' => 'Penerbit',
        ]);
    }

    public function store(StorePublisherRequest $request): RedirectResponse
    {
        Publisher::create($request->validated());

        return redirect()->route('admin.publishers.index')->with('success', 'Penerbit berhasil ditambahkan.');
    }

    public function show(Publisher $publisher): View
    {
        return view('admin.catalog.show', [
            'title' => $publisher->name,
            'resource' => 'publishers',
            'label' => 'Penerbit',
            'item' => $publisher,
            'books' => Book::query()
                ->with(['author', 'genre'])
                ->whereBelongsTo($publisher)
                ->orderBy('title')
                ->paginate(12),
        ]);
    }

    public function edit(Publisher $publisher): View
    {
        return view('admin.catalog.edit', [
            'title' => 'Ubah Penerbit',
            'resource' => 'publishers',
            'label' => 'Penerbit',
            'item' => $publisher,
        ]);
    }

    public function update(StorePublisherRequest $request, Publisher $publisher): RedirectResponse
    {
        $publisher->update($request->validated());

        return redirect()->route('admin.publishers.index')->with('success', 'Penerbit berhasil diperbarui.');
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        $publisher->delete();

        return redirect()->route('admin.publishers.index')->with('success', 'Penerbit dihapus.');
    }
}
