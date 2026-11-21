<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publisher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->string('isbn')->nullable()->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('stock')->default(1);
            $table->string('cover_path')->nullable();
            $table->string('google_books_id')->nullable();
            $table->decimal('average_rating', 2, 1)->default(0);
            $table->boolean('editor_pick')->default(false);
            $table->boolean('award_winner')->default(false);
            $table->string('award_name')->nullable();
            $table->boolean('public_figure_pick')->default(false);
            $table->string('public_figure_name')->nullable();
            $table->boolean('social_trend')->default(false);
            $table->string('social_trend_source')->nullable();
            $table->json('themes')->nullable();
            $table->timestamps();

            $table->index(['genre_id', 'title']);
            $table->index(['editor_pick', 'award_winner']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('genres');
    }
};
