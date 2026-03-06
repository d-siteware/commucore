<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailing_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Multilingual subject & message (JSON: {"de": "...", "hu": "..."})
            $table->json('subject');
            $table->json('message');

            // Optional link
            $table->string('url')->nullable();
            $table->json('url_label')->nullable(); // {"de": "...", "hu": "..."}

            // Attachments: [{"locale": "de", "original": "file.pdf"}, ...]
            // Note: actual files are deleted after sending – only filenames are kept for documentation
            $table->json('attachments')->nullable();

            // Send options
            $table->boolean('include_mailing_list')->default(false);
            $table->boolean('set_link')->default(true);
            $table->boolean('set_attachment')->default(true);
            $table->boolean('set_personal_greeting')->default(true);

            // How many recipients actually received the mail
            $table->unsignedInteger('recipient_count')->default(0);

            // Breakdown: how many were members vs mailing-list subscribers
            $table->unsignedInteger('member_count')->default(0);
            $table->unsignedInteger('mailing_list_count')->default(0);

            $table->timestamps(); // created_at = sent_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_mailings');
    }
};
