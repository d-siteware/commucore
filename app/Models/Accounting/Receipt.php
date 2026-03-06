<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Models\Traits\HasHistory;
use Database\Factories\ReceiptFactory;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Imagick;

/**
 * @property int $id
 * @property string|null $file_name
 * @property string|null $file_name_original
 * @property int $transaction_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Transaction|null $transaction
 *
 * @method static Builder<static>|Receipt newModelQuery()
 * @method static Builder<static>|Receipt newQuery()
 * @method static Builder<static>|Receipt query()
 * @method static Builder<static>|Receipt whereCreatedAt($value)
 * @method static Builder<static>|Receipt whereFileName($value)
 * @method static Builder<static>|Receipt whereFileNameOriginal($value)
 * @method static Builder<static>|Receipt whereId($value)
 * @method static Builder<static>|Receipt whereTransactionId($value)
 * @method static Builder<static>|Receipt whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\History> $histories
 * @property-read int|null $histories_count
 *
 * @mixin Eloquent
 */
final class Receipt extends Model
{
    /** @use HasFactory<ReceiptFactory> */
    use HasFactory;

    use HasHistory;

    protected $guarded = [];

    public function transaction(): BelongsTo
    {
        return $this->BelongsTo(Transaction::class);
    }

    public function download(): void
    {
        // TODO : $this->file_name;
    }

    public function getPreviewUrl(): string
    {
        return route('secure-image.category', ['category' => 'accounting/receipts', 'filename' => $this->file_name]);
    }

    public function _getPreviewUrl($file_name): string
    {
        return pathinfo($file_name, PATHINFO_FILENAME);

        //        if (Storage::disk('local')->exists('accounting/receipts/'.$file_name)) {
        //
        //            $file = pathinfo($file_name, PATHINFO_FILENAME);
        //
        //            if (Storage::disk('local')->exists('accounting/receipts/previews/'.$file.'.png')) {
        //                return asset('storage/app/private/accounting/receipts/previews/'.$file.'.png');
        //            } else {
        //                return self::makePreview($file_name);
        //            }
        //
        //        } else{
        //            return '';
        //        }

    }

    public static function makePreview(string $filename): string
    {
        $pdfFullPath = storage_path('app/private/accounting/receipts/'.$filename);
        $outputPath = 'accounting/receipts/previews/'.pathinfo($filename, PATHINFO_FILENAME).'.png';
        $outputFullPath = storage_path('app/private/'.$outputPath);

        // Ensure directory exists
        $outputDir = dirname($outputFullPath);
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Detect Ghostscript path
        $gsPath = '/usr/bin/gs'; // Linux default
        if (app()->environment() == 'local') {
            $gsPath = '/opt/homebrew/bin/gs'; // macOS Homebrew
        }
        putenv("MAGICK_GS_DELEGATE=$gsPath");

        try {
            $imagick = new Imagick;
            $imagick->readImage($pdfFullPath.'[0]');
            $w = (int) round($imagick->getImageWidth() * 0.3);
            $h = (int) round($imagick->getImageHeight() * 0.3);
            $imagick->resizeImage($w, $h, Imagick::FILTER_CATROM, 1);
            $imagick->setResolution(288, 288);
            $imagick->setImageFormat('png');
            $imagick->writeImage($outputFullPath);
            $imagick->clear();

            return Storage::url($outputPath);
        } catch (Exception $e) {
            Log::error('PDF Preview Error: '.$e->getMessage());

            return '';
        }
    }
}
