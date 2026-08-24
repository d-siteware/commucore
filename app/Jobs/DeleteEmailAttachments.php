<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class DeleteEmailAttachments
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $attachments;

    public function __construct(array $attachments)
    {
        $this->attachments = $attachments;
    }

    public function handle(): void
    {
        foreach ($this->attachments as $filePath) {
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                Log::info("Deleted email attachment: $filePath");
            } else {
                Log::warning("Tried to delete non-existing file: $filePath");
            }
        }
    }
}
