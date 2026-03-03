<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by_user_id')
                ->constrained('users')
                ->restrictOnDelete(); // User nicht löschbar solange Dokumente existieren

            // Storage
            $table->uuid('uuid')->unique();                    // Dateiname im Storage
            $table->string('original_name');                   // Originaler Dateiname für Download
            $table->string('disk')->default('private');        // Immer private!
            $table->string('path');                            // Relativer Pfad: member-documents/{uuid}
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');                // Dateigröße in Bytes

            // Klassifizierung
            $table->string('category', 50);                    // Enum-Wert als String
            $table->text('notes')->nullable();                 // Optionale Notiz vom Uploader

            // Audit
            $table->timestamp('last_accessed_at')->nullable(); // Wann zuletzt geöffnet
            $table->foreignId('last_accessed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();                             // Nie hart löschen!
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_documents');
    }
};
