<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Seed a curated starter catalog with recommendation metadata.
     */
    public function run(): void
    {
        $genres = [
            'Fiksi' => 'Cerita rekaan yang menjelajahi karakter, konflik, dan imajinasi penulis.',
            'Nonfiksi' => 'Buku berbasis fakta, riset, dan pengalaman nyata untuk memperluas wawasan.',
            'Sains' => 'Buku populer dan akademik tentang alam, teknologi, dan penemuan.',
            'Sejarah' => 'Rekam jejak peristiwa, budaya, dan tokoh yang membentuk dunia.',
            'Biografi' => 'Kisah hidup tokoh yang menginspirasi dan memberi pelajaran.',
            'Puisi' => 'Kumpulan karya liris yang memainkan rasa dan makna kata.',
            'Cerpen' => 'Cerita pendek dengan konflik ringkas dan kesan mendalam.',
            'Teknologi' => 'Buku tentang pemrograman, produk digital, dan masa depan teknologi.',
        ];

        foreach ($genres as $name => $description) {
            Genre::updateOrCreate(['name' => $name], ['description' => $description]);
        }

        $publishers = [
            'Bentang Pustaka' => 'Yogyakarta',
            'Lentera Dipantara' => 'Jakarta',
            'Kepustakaan Populer Gramedia' => 'Jakarta',
            'Gramedia Pustaka Utama' => 'Jakarta',
            'Kompas' => 'Jakarta',
            'Republika' => 'Jakarta',
            'Pastel Books' => 'Bandung',
            'Mizan' => 'Bandung',
        ];

        foreach ($publishers as $name => $city) {
            Publisher::updateOrCreate(['name' => $name], [
                'city' => $city,
                'description' => 'Penerbit buku dengan katalog pilihan untuk pembaca Indonesia.',
            ]);
        }

        $authors = [
            'Andrea Hirata' => 'Penulis Laskar Pelangi yang dikenal dengan kisah pendidikan dari Belitung.',
            'Pramoedya Ananta Toer' => 'Sastrawan Indonesia dengan karya-karya sejarah yang mendunia.',
            'Dee Lestari' => 'Penulis dan musisi yang dikenal lewat serial Supernova dan Perahu Kertas.',
            'Leila S. Chudori' => 'Jurnalis dan penulis novel sejarah kontemporer Indonesia.',
            'Eka Kurniawan' => 'Novelis yang karyanya telah diterjemahkan ke banyak bahasa.',
            'Henry Manampiring' => 'Penulis buku pengembangan diri berbasis filsafat Stoa.',
            'James Clear' => 'Penulis dan pembicara tentang kebiasaan serta produktivitas.',
            'Yuval Noah Harari' => 'Sejarawan dan penulis Sapiens serta Homo Deus.',
            'Ahmad Fuadi' => 'Penulis Negeri 5 Menara yang mengangkat semangat pendidikan.',
            'Pidi Baiq' => 'Penulis dan musisi yang dikenal lewat serial Dilan.',
            'Matt Haig' => 'Penulis Inggris yang menulis tentang kesehatan mental dan harapan.',
            'Tere Liye' => 'Penulis produktif dengan banyak novel populer Indonesia.',
            'Makoto Shinkai' => 'Sutradara dan penulis Jepang yang karyanya sering diadaptasi.',
        ];

        foreach ($authors as $name => $description) {
            Author::updateOrCreate(['name' => $name], ['description' => $description]);
        }

        $books = [
            [
                'title' => 'Laskar Pelangi',
                'genre' => 'Fiksi',
                'publisher' => 'Bentang Pustaka',
                'author' => 'Andrea Hirata',
                'isbn' => '9789793062792',
                'average_rating' => 4.8,
                'description' => 'Kisah sepuluh anak Belitung yang berjuang mendapatkan pendidikan layak.',
                'editor_pick' => true,
                'award_winner' => true,
                'award_name' => 'Penghargaan Pembaca Indonesia',
                'themes' => ['Pendidikan', 'Keluarga', 'Awal Tahun'],
            ],
            [
                'title' => 'Bumi Manusia',
                'genre' => 'Sejarah',
                'publisher' => 'Lentera Dipantara',
                'author' => 'Pramoedya Ananta Toer',
                'isbn' => '9789799731234',
                'average_rating' => 4.9,
                'description' => 'Kisah Minke di akhir era kolonial yang menggugah kesadaran sejarah.',
                'award_winner' => true,
                'award_name' => 'Karya Sastra Dunia',
                'public_figure_pick' => true,
                'public_figure_name' => 'Daftar bacaan sastrawan nasional',
                'themes' => ['Sejarah', 'Kemerdekaan'],
            ],
            [
                'title' => 'Perahu Kertas',
                'genre' => 'Fiksi',
                'publisher' => 'Bentang Pustaka',
                'author' => 'Dee Lestari',
                'isbn' => '9786020360447',
                'average_rating' => 4.5,
                'description' => 'Perjalanan Kugy dan Keenan mencari jalan hidup dan cinta.',
                'social_trend' => true,
                'social_trend_source' => 'Adaptasi film & tren media sosial',
                'themes' => ['Cinta', 'Liburan'],
            ],
            [
                'title' => 'Pulang',
                'genre' => 'Sejarah',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'author' => 'Leila S. Chudori',
                'isbn' => '9789799105875',
                'average_rating' => 4.7,
                'description' => 'Kisah eksil politik Indonesia yang merindukan rumah.',
                'award_winner' => true,
                'award_name' => 'Khatulistiwa Literary Award',
                'themes' => ['Keluarga', 'Sejarah'],
            ],
            [
                'title' => 'Cantik Itu Luka',
                'genre' => 'Fiksi',
                'publisher' => 'Gramedia Pustaka Utama',
                'author' => 'Eka Kurniawan',
                'isbn' => '9786020317434',
                'average_rating' => 4.6,
                'description' => 'Saga keluarga dengan realisme magis dan sejarah kelam Indonesia.',
                'award_winner' => true,
                'award_name' => 'World Readers Award',
                'social_trend' => true,
                'social_trend_source' => 'Dibicarakan di komunitas sastra daring',
                'themes' => ['Sejarah', 'Kemerdekaan'],
            ],
            [
                'title' => 'Filosofi Teras',
                'genre' => 'Nonfiksi',
                'publisher' => 'Kompas',
                'author' => 'Henry Manampiring',
                'isbn' => '9786024125189',
                'average_rating' => 4.7,
                'description' => 'Pengantar filsafat Stoa untuk hidup yang lebih tenang.',
                'editor_pick' => true,
                'themes' => ['Awal Tahun', 'Sepanjang Tahun'],
            ],
            [
                'title' => 'Atomic Habits',
                'genre' => 'Nonfiksi',
                'publisher' => 'Gramedia Pustaka Utama',
                'author' => 'James Clear',
                'isbn' => '9786020633176',
                'average_rating' => 4.8,
                'description' => 'Cara membangun kebiasaan kecil yang memberi hasil besar.',
                'social_trend' => true,
                'social_trend_source' => 'Tren produktivitas media sosial',
                'themes' => ['Awal Tahun', 'Sepanjang Tahun'],
            ],
            [
                'title' => 'Sapiens',
                'genre' => 'Sains',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'author' => 'Yuval Noah Harari',
                'isbn' => '9786024242145',
                'average_rating' => 4.6,
                'description' => 'Sejarah singkat umat manusia dari zaman batu hingga era digital.',
                'public_figure_pick' => true,
                'public_figure_name' => 'Rekomendasi tokoh teknologi',
                'themes' => ['Sejarah', 'Sepanjang Tahun'],
            ],
            [
                'title' => 'Negeri 5 Menara',
                'genre' => 'Fiksi',
                'publisher' => 'Gramedia Pustaka Utama',
                'author' => 'Ahmad Fuadi',
                'isbn' => '9789792248203',
                'average_rating' => 4.5,
                'description' => 'Enam santri dan mantra man jadda wajada.',
                'editor_pick' => true,
                'themes' => ['Pendidikan', 'Keluarga'],
            ],
            [
                'title' => 'Dilan 1990',
                'genre' => 'Fiksi',
                'publisher' => 'Pastel Books',
                'author' => 'Pidi Baiq',
                'isbn' => '9786027870414',
                'average_rating' => 4.3,
                'description' => 'Kisah cinta remaja Bandung di awal 1990-an.',
                'social_trend' => true,
                'social_trend_source' => 'Adaptasi film fenomenal',
                'public_figure_pick' => true,
                'public_figure_name' => 'Bacaan populer anak muda',
                'themes' => ['Cinta', 'Liburan'],
            ],
            [
                'title' => 'The Midnight Library',
                'genre' => 'Fiksi',
                'publisher' => 'Gramedia Pustaka Utama',
                'author' => 'Matt Haig',
                'isbn' => '9786020643021',
                'average_rating' => 4.4,
                'description' => 'Perpustakaan di antara hidup dan mati berisi kemungkinan hidup.',
                'social_trend' => true,
                'social_trend_source' => 'BookTok & rekomendasi media sosial',
                'themes' => ['Liburan', 'Awal Tahun'],
            ],
            [
                'title' => 'Laut Bercerita',
                'genre' => 'Sejarah',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'author' => 'Leila S. Chudori',
                'isbn' => '9786024246945',
                'average_rating' => 4.8,
                'description' => 'Kisah aktivis 1998 dan mereka yang ditinggalkan.',
                'award_winner' => true,
                'award_name' => 'Pilihan Pembaca Sastra',
                'themes' => ['Sejarah', 'Kemerdekaan'],
            ],
            [
                'title' => 'Tentang Kamu',
                'genre' => 'Fiksi',
                'publisher' => 'Republika',
                'author' => 'Tere Liye',
                'isbn' => '9786020821337',
                'average_rating' => 4.2,
                'description' => 'Perjalanan menemukan kisah hidup seorang perempuan.',
                'themes' => ['Keluarga', 'Cinta'],
            ],
            [
                'title' => 'Your Name',
                'genre' => 'Fiksi',
                'publisher' => 'Mizan',
                'author' => 'Makoto Shinkai',
                'isbn' => '9786024411299',
                'average_rating' => 4.4,
                'description' => 'Dua remaja terhubung lewat mimpi dan jarak waktu.',
                'social_trend' => true,
                'social_trend_source' => 'Adaptasi anime populer',
                'themes' => ['Cinta', 'Liburan'],
            ],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(
                ['title' => $book['title']],
                [
                    'genre_id' => Genre::where('name', $book['genre'])->value('id'),
                    'publisher_id' => Publisher::where('name', $book['publisher'])->value('id'),
                    'author_id' => Author::where('name', $book['author'])->value('id'),
                    'isbn' => $book['isbn'],
                    'description' => $book['description'],
                    'average_rating' => $book['average_rating'],
                    'editor_pick' => $book['editor_pick'] ?? false,
                    'award_winner' => $book['award_winner'] ?? false,
                    'award_name' => $book['award_name'] ?? null,
                    'public_figure_pick' => $book['public_figure_pick'] ?? false,
                    'public_figure_name' => $book['public_figure_name'] ?? null,
                    'social_trend' => $book['social_trend'] ?? false,
                    'social_trend_source' => $book['social_trend_source'] ?? null,
                    'themes' => $book['themes'] ?? [],
                ],
            );
        }
    }
}
