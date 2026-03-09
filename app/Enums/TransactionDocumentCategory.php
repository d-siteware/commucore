<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionDocumentCategory: string implements \App\Enums\Contracts\HasLabel
{
    case Invoice = 'invoice';       // Rechnung
    case Receipt = 'receipt';       // Quittung / Kassenbon
    case BankStatement = 'bank_statement'; // Kontoauszug
    case Contract = 'contract';      // Vertrag
    case Other = 'other';         // Sonstiges

    public function label(string $locale = 'de'): string
    {
        return __('transaction.documents.category.'.$this->value);
    }

    public function allowedMimeTypes(): array
    {
        return match ($this) {
            self::Invoice,
            self::BankStatement,
            self::Contract => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            self::Receipt => [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/tiff',
            ],
            self::Other => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg',
                'image/png',
                'image/tiff',
            ],
        };
    }

    public function allowedExtensions(): array
    {
        return match ($this) {
            self::Invoice,
            self::BankStatement,
            self::Contract => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            self::Receipt => ['pdf', 'jpg', 'jpeg', 'png', 'tif', 'tiff'],
            self::Other => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'tif', 'tiff'],
        };
    }

    public function isMimeTypeAllowed(string $mimeType): bool
    {
        return in_array($mimeType, $this->allowedMimeTypes(), strict: true);
    }

    public static function selectOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public static function extensionsForValidation(): string
    {
        return collect(self::cases())
            ->flatMap(fn (self $case): array => $case->allowedExtensions())
            ->unique()
            ->implode(',');
    }
}
