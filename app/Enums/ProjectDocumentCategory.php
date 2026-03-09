<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectDocumentCategory: string implements \App\Enums\Contracts\HasLabel
{
    case Planning = 'planning';      // Planung / Konzept
    case Contract = 'contract';      // Vertrag / Kooperationsvereinbarung
    case Report = 'report';        // Sachbericht / Abschlussbericht
    case Invoice = 'invoice';       // Rechnung / Kostenaufstellung
    case Correspondence = 'correspondence'; // Briefverkehr / E-Mails
    case Photo = 'photo';         // Fotos / Dokumentation
    case Other = 'other';         // Sonstiges

    public function label(string $locale = 'de'): string
    {
        return __('projects.documents.category.'.$this->value);
    }

    public function allowedMimeTypes(): array
    {
        return match ($this) {
            self::Planning,
            self::Contract,
            self::Report,
            self::Invoice => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
            self::Correspondence => [
                'application/pdf',
                'message/rfc822',
                'application/octet-stream',
                'image/jpeg',
                'image/png',
            ],
            self::Photo => [
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
                'message/rfc822',
                'application/octet-stream',
                'image/jpeg',
                'image/png',
                'image/tiff',
            ],
        };
    }

    public function allowedExtensions(): array
    {
        return match ($this) {
            self::Planning,
            self::Contract,
            self::Report,
            self::Invoice => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
            self::Correspondence => ['pdf', 'eml', 'jpg', 'jpeg', 'png'],
            self::Photo => ['jpg', 'jpeg', 'png', 'tif', 'tiff'],
            self::Other => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'eml', 'jpg', 'jpeg', 'png', 'tif', 'tiff'],
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
