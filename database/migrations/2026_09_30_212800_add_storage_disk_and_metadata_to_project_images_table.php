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
        Schema::table('project_images', function (Blueprint $table) {
            if (! Schema::hasColumn('project_images', 'storage_disk')) {
                $table->string('storage_disk', 20)->default('public')->after('type');
            }
            if (! Schema::hasColumn('project_images', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('video_url');
            }
            if (! Schema::hasColumn('project_images', 'mime_type')) {
                $table->string('mime_type', 50)->nullable()->after('file_size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('project_images', 'mime_type')) {
                $columnsToDrop[] = 'mime_type';
            }
            if (Schema::hasColumn('project_images', 'file_size')) {
                $columnsToDrop[] = 'file_size';
            }
            if (Schema::hasColumn('project_images', 'storage_disk')) {
                $columnsToDrop[] = 'storage_disk';
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
