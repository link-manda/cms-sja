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
        Schema::table('project_images', function (Blueprint $table) {
            $table->string('type', 20)->default('image')->after('project_id');
            $table->string('image_path')->nullable()->change();
            $table->string('video_url', 500)->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollback: purge any video records or records with NULL image_path
        // before reverting image_path to NOT NULL to prevent MySQL error 1138/1265
        DB::table('project_images')
            ->where('type', 'video')
            ->orWhereNull('image_path')
            ->delete();

        Schema::table('project_images', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
            $table->dropColumn(['type', 'video_url']);
        });
    }
};
