<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_images', function (Blueprint $table) {
            if (! Schema::hasColumn('post_images', 'filename')) {
                return;
            }

            $table->after('filename', function (Blueprint $table) {
                $table->string('filename_small')->nullable();
                $table->string('filename_medium')->nullable();
                $table->string('filename_large')->nullable();
            });
        });
    }

    public function down(): void
    {
        Schema::table('post_images', function (Blueprint $table) {
            $table->dropColumn(['filename_small', 'filename_medium', 'filename_large']);
        });
    }
};
