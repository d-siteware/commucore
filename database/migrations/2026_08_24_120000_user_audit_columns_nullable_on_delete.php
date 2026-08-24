<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit-Spalten überleben die Löschung des verknüpften Users:
     * nullable + nullOnDelete statt restrict/cascade-blockierend.
     *
     * Hintergrund: Die Selbstlöschung crashte mit einer FK-Exception, sobald
     * der User Records hatte (Posts, Berichte, DATEV-Exporte, Mail-History,
     * Dokument-Uploads) — der Account ließ sich dann gar nicht mehr löschen
     * und blockierte damit auch den Recovery-Flow.
     *
     * Bewusste semantische Änderung bei member_documents.uploaded_by_user_id:
     * Der Upload-Verweis wird null, das Dokument bleibt beim Mitglied.
     */
    public function up(): void
    {
        foreach ($this->columns() as $table => $column) {
            Schema::table($table, function (Blueprint $blueprint) use ($column): void {
                $blueprint->dropForeign([$column]);
                $blueprint->unsignedBigInteger($column)->nullable()->change();
            });

            Schema::table($table, function (Blueprint $blueprint) use ($column): void {
                $blueprint->foreign($column)->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->columns() as $table => $column) {
            Schema::table($table, function (Blueprint $blueprint) use ($column): void {
                $blueprint->dropForeign([$column]);
                $blueprint->unsignedBigInteger($column)->nullable(false)->change();
            });

            Schema::table($table, function (Blueprint $blueprint) use ($column): void {
                $blueprint->foreign($column)->references('id')->on('users')->restrictOnDelete();
            });
        }
    }

    /** @return array<string, string> */
    private function columns(): array
    {
        return [
            'posts' => 'user_id',
            'account_reports' => 'created_by',
            'mail_history_entries' => 'user_id',
            'datev_exports' => 'exported_by',
            'member_documents' => 'uploaded_by_user_id',
        ];
    }
};
