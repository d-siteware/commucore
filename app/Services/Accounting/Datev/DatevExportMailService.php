<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

use App\Mail\DatevExportMail;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\DatevExport;
use App\Models\Accounting\Transaction;
use App\Services\Accounting\DatevSettingsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

final class DatevExportMailService
{
    public function __construct(
        private readonly DatevExportService $exportService,
        private readonly DatevSettingsService $settings,
    ) {}

    public function sendForReport(AccountReport $report): DatevExport
    {
        $storagePath = $this->exportService->exportForReport($report);

        $transactions = $this->loadTransactionsForReport($report);

        [$zipPath, $hash] = $this->buildZip($report, $storagePath, $transactions);

        $recipient = $this->settings->recipientEmail();

        $datevExport = DatevExport::create([
            'account_report_id' => $report->id,
            'exported_by' => auth()->id(),
            'filename' => basename($storagePath),
            'zip_path' => $zipPath,
            'zip_hash' => $hash,
            'sent_to_email' => $recipient,
            'exported_at' => now(),
        ]);

        $url = \URL::temporarySignedRoute(
            'datev-export.download',
            now()->addDays(7),
            ['datevExport' => $datevExport->id],
        );

        Mail::to($recipient)
            ->locale(auth()->user()->locale ?? config('app.locale'))
            ->send(new DatevExportMail($report, $url, $hash));

        return $datevExport;
    }

    /**
     * @return Collection<int, Transaction>
     */
    private function loadTransactionsForReport(AccountReport $report): Collection
    {
        return Transaction::query()
            ->with(['bookingAccount', 'account', 'receipts', 'documents'])
            ->where('account_id', $report->account_id)
            ->datevExportable()
            ->whereBetween('date', [
                $report->period_start->startOfDay(),
                $report->period_end->endOfDay(),
            ])
            ->orderBy('date')
            ->get();
    }

    /**
     * @param  Collection<int, Transaction>  $transactions
     * @return array{0: string, 1: string} [zipRelativePath, sha256Hash]
     */
    private function buildZip(AccountReport $report, string $csvStoragePath, Collection $transactions): array
    {
        $slug = $report->period_start->format('Y-m')
            .'_'.str_replace(' ', '-', $report->account->name ?? 'bericht');
        $zipFilename = 'datev/zips/'.Str::random(40).'.zip';
        $disk = Storage::disk('local');
        $zipFullPath = $disk->path($zipFilename);

        $disk->makeDirectory(dirname($zipFilename));

        $zip = new ZipArchive;
        if ($zip->open($zipFullPath, ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Konnte ZIP-Archiv nicht erstellen: '.$zipFullPath);
        }

        $csvName = 'EXTF_Buchungsstapel_'.$slug.'.csv';
        $csvContent = Storage::disk('local')->get('private/'.$csvStoragePath);
        if ($csvContent !== null) {
            $zip->addFromString($csvName, $csvContent);
        }

        $usedFilenames = [];
        foreach ($transactions->groupBy('account_id') as $accountTransactions) {
            $account = $accountTransactions->first()->account;
            $folderName = $account->type->value.' ('.$account->name.')';

            /** @var Transaction $transaction */
            foreach ($accountTransactions as $transaction) {
                $attachments = [];

                foreach ($transaction->documents as $document) {
                    $documentDisk = Storage::disk($document->disk);

                    if (! $documentDisk->exists($document->path)) {
                        continue;
                    }

                    $attachments[] = [
                        'path' => $documentDisk->path($document->path),
                        'original_name' => $document->original_name,
                    ];
                }

                foreach ($transaction->receipts as $receipt) {
                    if ($receipt->file_name === null) {
                        continue;
                    }

                    $receiptPath = $disk->path('accounting/receipts/'.$receipt->file_name);
                    if (! file_exists($receiptPath)) {
                        continue;
                    }

                    $attachments[] = [
                        'path' => $receiptPath,
                        'original_name' => $receipt->file_name_original ?? $receipt->file_name,
                    ];
                }

                foreach ($attachments as $attachment) {
                    $filename = $this->buildAttachmentFilename($transaction);
                    $extension = pathinfo($attachment['original_name'], PATHINFO_EXTENSION);
                    $extensionSuffix = $extension !== '' ? '.'.$extension : '';
                    $zipEntry = $folderName.'/'.$filename.$extensionSuffix;

                    if (isset($usedFilenames[$zipEntry])) {
                        $zipEntry = $folderName.'/'.$filename.'_'.($usedFilenames[$zipEntry]++).$extensionSuffix;
                    } else {
                        $usedFilenames[$zipEntry] = 1;
                    }

                    $zip->addFile($attachment['path'], $zipEntry);
                }
            }
        }

        $zip->close();

        return [$zipFilename, hash_file('sha256', $zipFullPath)];
    }

    private function buildAttachmentFilename(Transaction $transaction): string
    {
        $date = $transaction->date->format('Y-m-d');
        $id = $transaction->id;
        $amount = number_format(abs($transaction->amount_gross) / 100, 2, ',', '');

        $sign = $transaction->type->multiplier() >= 0 ? '+' : '-';

        $label = Str::limit(
            preg_replace('/[^a-zA-Z0-9äöüÄÖÜß\-_ ]/', '', $transaction->label),
            40,
            '',
        );

        return $date.'_'.$id.'_'.$sign.$amount.'€_'.$label;
    }

    public static function cleanupOldZips(int $olderThanDays = 30): int
    {
        $exports = DatevExport::query()
            ->whereNotNull('zip_path')
            ->where('exported_at', '<', now()->subDays($olderThanDays))
            ->get();

        $count = 0;
        foreach ($exports as $export) {
            $fullPath = storage_path('app/private/'.$export->zip_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $export->update(['zip_path' => null]);
            $count++;
        }

        return $count;
    }
}
