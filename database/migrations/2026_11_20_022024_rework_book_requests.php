<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_requests', function (Blueprint $table): void {
            $table->string('genre')->nullable()->after('title');
            $table->string('publisher')->nullable()->after('genre');
            $table->string('author')->nullable()->after('publisher');
        });

        foreach (DB::table('book_requests')->get(['id', 'genre_id', 'publisher_id', 'author_id']) as $bookRequest) {
            DB::table('book_requests')->where('id', $bookRequest->id)->update([
                'genre' => $bookRequest->genre_id ? DB::table('genres')->where('id', $bookRequest->genre_id)->value('name') : null,
                'publisher' => $bookRequest->publisher_id ? DB::table('publishers')->where('id', $bookRequest->publisher_id)->value('name') : null,
                'author' => $bookRequest->author_id ? DB::table('authors')->where('id', $bookRequest->author_id)->value('name') : null,
            ]);
        }

        Schema::table('book_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('genre_id');
            $table->dropConstrainedForeignId('publisher_id');
            $table->dropConstrainedForeignId('author_id');
            $table->dropColumn('request_type');
        });

        Schema::create('book_request_votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_request_id')->constrained()->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();

            $table->unique(['user_id', 'book_request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_request_votes');

        Schema::table('book_requests', function (Blueprint $table): void {
            $table->string('request_type', 16)->default('title')->after('reviewed_by');
            $table->foreignId('genre_id')->nullable()->constrained()->nullOnDelete()->after('title');
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete()->after('genre');
            $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete()->after('publisher');
        });

        foreach (DB::table('book_requests')->get(['id', 'genre', 'publisher', 'author']) as $bookRequest) {
            DB::table('book_requests')->where('id', $bookRequest->id)->update([
                'genre_id' => $bookRequest->genre ? DB::table('genres')->where('name', $bookRequest->genre)->value('id') : null,
                'publisher_id' => $bookRequest->publisher ? DB::table('publishers')->where('name', $bookRequest->publisher)->value('id') : null,
                'author_id' => $bookRequest->author ? DB::table('authors')->where('name', $bookRequest->author)->value('id') : null,
            ]);
        }

        Schema::table('book_requests', function (Blueprint $table): void {
            $table->dropColumn(['genre', 'publisher', 'author']);
        });
    }
};
