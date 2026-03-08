<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id')
                ->nullable()
                ->after('event_id');

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
