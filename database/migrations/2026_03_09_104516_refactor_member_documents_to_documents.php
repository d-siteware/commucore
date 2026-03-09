<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table): void {
            $table->id();

            // Polymorphisch statt member_id
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            $table->index(['documentable_type', 'documentable_id']);

            $table->foreignId('uploaded_by_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Storage – identisch zu member_documents
            $table->uuid('uuid')->unique();
            $table->string('original_name');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');

            // Klassifizierung
            $table->string('category', 50)->nullable();
            $table->string('label')->nullable();        // optionale Bezeichnung
            $table->text('notes')->nullable();

            // Audit
            $table->timestamp('last_accessed_at')->nullable();
            $table->foreignId('last_accessed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Bestehende member_documents migrieren
        DB::statement("
            INSERT INTO documents (
                documentable_type, documentable_id,
                uploaded_by_user_id, uuid, original_name,
                disk, path, mime_type, size,
                category, notes,
                last_accessed_at, last_accessed_by_user_id,
                created_at, updated_at, deleted_at
            )
            SELECT
                'App\\\\Models\\\\Membership\\\\Member' as documentable_type,
                member_id as documentable_id,
                uploaded_by_user_id, uuid, original_name,
                disk, path, mime_type, size,
                category, notes,
                last_accessed_at, last_accessed_by_user_id,
                created_at, updated_at, deleted_at
            FROM member_documents
        ");

        // Alte Tabelle nach Migration behalten (Safety) – kann später gedroppt werden
        // Schema::dropIfExists('member_documents');
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
