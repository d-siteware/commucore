<?php

declare(strict_types=1);

use App\Enums\EventStatus;
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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->json('title');
            $table->json('slug')->unique();
            $table->json('body');
            $table->foreignId('user_id')->constrained('users');
            $table->string('status', EventStatus::DRAFT->value);
        });

        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->json('caption')->nullable();
            $table->string('filename');
            $table->string('original_filename');
            $table->foreignId('post_id')->constrained();
        });
    }
};
