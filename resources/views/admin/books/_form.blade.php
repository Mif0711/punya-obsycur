@php
    $book = $book ?? null;
    $themeOptions = array_values(array_unique(array_merge([
        'Awal Tahun', 'Cinta', 'Bumi', 'Keluarga', 'Kemerdekaan', 'Liburan', 'Sepanjang Tahun',
        'Pendidikan', 'Sejarah', 'Filsafat', 'Produktivitas',
    ], old('themes', $book?->themes ?? []))));
    $selectedThemes = old('themes', $book?->themes ?? []);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6">
            <section class="surface-card space-y-5 p-6">
                <div class="flex items-center gap-2 border-b border-line pb-3">
                    <x-icon name="book" class="h-4 w-4 text-accent" />
                    <h2 class="text-sm font-bold uppercase tracking-[0.14em] text-ink">Informasi Utama</h2>
                </div>

                <div>
                    <label for="title" class="field-label">Judul Buku</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $book?->title) }}" required maxlength="255" class="field-input" />
                    @error('title') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="genre_id" class="field-label">Genre</label>
                        <select id="genre_id" name="genre_id" required class="field-input">
                            <option value="">Pilih genre</option>
                            @foreach ($genres as $genre)
                                <option value="{{ $genre->id }}" @selected((int) old('genre_id', $book?->genre_id) === $genre->id)>{{ $genre->name }}</option>
                            @endforeach
                        </select>
                        @error('genre_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="publisher_id" class="field-label">Penerbit</label>
                        <select id="publisher_id" name="publisher_id" required class="field-input">
                            <option value="">Pilih penerbit</option>
                            @foreach ($publishers as $publisher)
                                <option value="{{ $publisher->id }}" @selected((int) old('publisher_id', $book?->publisher_id) === $publisher->id)>{{ $publisher->name }}</option>
                            @endforeach
                        </select>
                        @error('publisher_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="author_id" class="field-label">Pengarang</label>
                        <select id="author_id" name="author_id" required class="field-input">
                            <option value="">Pilih pengarang</option>
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}" @selected((int) old('author_id', $book?->author_id) === $author->id)>{{ $author->name }}</option>
                            @endforeach
                        </select>
                        @error('author_id') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="isbn" class="field-label">ISBN</label>
                    <input id="isbn" name="isbn" type="text" value="{{ old('isbn', $book?->isbn) }}" maxlength="30" class="field-input" placeholder="978…" />
                    @error('isbn') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="field-label">Deskripsi</label>
                    <textarea id="description" name="description" rows="5" maxlength="5000" class="field-input resize-y" placeholder="Ringkasan isi buku…">{{ old('description', $book?->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="keterangan" class="field-label">Keterangan (Cover Belakang)</label>
                    <textarea id="keterangan" name="keterangan" rows="5" maxlength="5000" class="field-input resize-y" placeholder="Kesimpulan atau sinopsis yang biasanya tercetak di cover belakang buku…">{{ old('keterangan', $book?->keterangan) }}</textarea>
                    <p class="mt-1 text-xs text-muted">Isi dengan ringkasan singkat yang membantu pembaca memutuskan untuk meminjam.</p>
                    @error('keterangan') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </section>

            <section class="surface-card space-y-4 p-6">
                <div class="flex items-center gap-2 border-b border-line pb-3">
                    <x-icon name="sparkles" class="h-4 w-4 text-violet" />
                    <h2 class="text-sm font-bold uppercase tracking-[0.14em] text-ink">Sinyal Rekomendasi</h2>
                </div>
                <p class="text-xs text-muted">Penanda ini memberi bobot tambahan pada mesin rekomendasi 8 sinyal.</p>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        ['name' => 'editor_pick', 'label' => 'Pilihan Editor / Pustakawan', 'icon' => 'star', 'text' => 'text-accent'],
                        ['name' => 'award_winner', 'label' => 'Pemenang Penghargaan', 'icon' => 'star', 'text' => 'text-warning'],
                        ['name' => 'public_figure_pick', 'label' => 'Daftar Bacaan Tokoh Publik', 'icon' => 'users', 'text' => 'text-success'],
                        ['name' => 'social_trend', 'label' => 'Tren Media Sosial / Adaptasi', 'icon' => 'trending', 'text' => 'text-violet'],
                    ] as $flag)
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line bg-white/[0.02] p-3 transition hover:border-line-soft">
                            <input type="hidden" name="{{ $flag['name'] }}" value="0">
                            <input type="checkbox" name="{{ $flag['name'] }}" value="1" @checked((bool) old($flag['name'], $book?->{$flag['name']} ?? false))
                                class="mt-0.5 h-4 w-4 rounded border-line bg-canvas text-accent focus:ring-accent/40" />
                            <span>
                                <span class="flex items-center gap-1.5 text-sm font-semibold text-ink">
                                    <x-icon :name="$flag['icon']" class="h-3.5 w-3.5 {{ $flag['text'] }}" />
                                    {{ $flag['label'] }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="award_name" class="field-label">Nama Penghargaan</label>
                        <input id="award_name" name="award_name" type="text" value="{{ old('award_name', $book?->award_name) }}" maxlength="150" class="field-input" />
                    </div>
                    <div>
                        <label for="public_figure_name" class="field-label">Nama Tokoh Publik</label>
                        <input id="public_figure_name" name="public_figure_name" type="text" value="{{ old('public_figure_name', $book?->public_figure_name) }}" maxlength="150" class="field-input" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="social_trend_source" class="field-label">Sumber Tren Sosial</label>
                        <input id="social_trend_source" name="social_trend_source" type="text" value="{{ old('social_trend_source', $book?->social_trend_source) }}" maxlength="150" class="field-input" placeholder="contoh: BookTok, adaptasi film…" />
                    </div>
                </div>

                <div>
                    <span class="field-label">Tema / Momen</span>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($themeOptions as $theme)
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-line bg-white/[0.02] px-2.5 py-2 text-xs text-body transition hover:border-line-soft">
                                <input type="checkbox" name="themes[]" value="{{ $theme }}" @checked(in_array($theme, $selectedThemes, true))
                                    class="h-3.5 w-3.5 rounded border-line bg-canvas text-accent focus:ring-accent/40" />
                                {{ $theme }}
                            </label>
                        @endforeach
                    </div>
                    @error('themes') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="surface-card p-5">
                <h2 class="text-sm font-bold text-ink">Sampul Buku</h2>
                <div class="mt-3 overflow-hidden rounded-xl border border-line bg-surface-2">
                    @if ($book?->cover_path)
                        <img src="{{ asset('storage/'.$book->cover_path) }}" alt="Sampul saat ini" class="aspect-[2/3] w-full object-cover">
                    @else
                        <div class="grid aspect-[2/3] w-full place-items-center text-muted">
                            <div class="text-center">
                                <x-icon name="book" class="mx-auto h-8 w-8" />
                                <p class="mt-2 text-xs">Belum ada sampul</p>
                            </div>
                        </div>
                    @endif
                </div>
                <label for="cover" class="field-label mt-4">Unggah sampul</label>
                <input id="cover" name="cover" type="file" accept="image/png,image/jpeg,image/webp" class="field-input text-xs file:mr-3 file:rounded-md file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-xs file:text-body" />
                <p class="mt-1 text-[0.7rem] text-muted">JPG, PNG, atau WEBP · maks. 2 MB.</p>
                @error('cover') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
            </section>

            <section class="surface-card space-y-3 p-5">
                <button type="submit" class="btn btn-primary w-full">
                    <x-icon name="check" class="h-4 w-4" />
                    {{ $submitLabel }}
                </button>
                <a href="{{ route('admin.books.index') }}" class="btn btn-ghost w-full">Batal</a>
            </section>
        </aside>
    </div>
</form>
