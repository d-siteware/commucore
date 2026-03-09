<?php

declare(strict_types=1);

namespace App\Enums;

enum FundingDocumentCategory: string implements \App\Enums\Contracts\HasLabel
{
    case ApprovalNotice = 'approval_notice';   // Förderbescheid
    case UsageProof = 'usage_proof';        // Verwendungsnachweis
    case Correspondence = 'correspondence';     // Briefverkehr / E-Mails
    case Contract = 'contract';           // Vertrag / Vereinbarung
    case Report = 'report';             // Sachbericht / Zwischenbericht
    case Other = 'other';              // Sonstiges

    public function label(string $locale = 'de'): string
    {
        return __('fundings.documents.category.'.$this->value);
    }

    public function allowedMimeTypes(): array
    {
        return match ($this) {
            self::ApprovalNotice,
            self::UsageProof,
            self::Contract,
            self::Report => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            self::Correspondence => [
                'application/pdf',
                'message/rfc822',        // .eml
                'application/octet-stream', // .eml fallback
                'image/jpeg',
                'image/png',
            ],
            self::Other => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'message/rfc822',
                'application/octet-stream',
                'image/jpeg',
                'image/png',
            ],
        };
    }

    public function allowedExtensions(): array
    {
        return match ($this) {
            self::ApprovalNotice,
            self::UsageProof,
            self::Contract,
            self::Report => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            self::Correspondence => ['pdf', 'eml', 'jpg', 'jpeg', 'png'],
            self::Other => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'eml', 'jpg', 'jpeg', 'png'],
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
