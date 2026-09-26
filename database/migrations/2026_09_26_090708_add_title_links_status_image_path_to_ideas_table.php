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
        Schema::table('ideas', function (Blueprint $table): void {
            $table->string('title')->default('')->after('user_id');
            $table->json('links')->nullable()->after('description');
            $table->string('status')->default('pending')->after('links');
            $table->string('image_path')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ideas', function (Blueprint $table): void {
            $table->dropColumn(['title', 'links', 'status', 'image_path']);
        });
    }
};
