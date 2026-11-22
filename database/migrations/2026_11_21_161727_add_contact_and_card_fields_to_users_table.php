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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('parent_phone', 30)->nullable()->after('phone');
            $table->string('nickname', 100)->nullable()->after('name');
            $table->string('birth_place', 150)->nullable()->after('nickname');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('school', 150)->nullable()->after('birth_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'parent_phone',
                'nickname',
                'birth_place',
                'birth_date',
                'school',
            ]);
        });
    }
};
