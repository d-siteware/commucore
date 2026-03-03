<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberDocumentCategory: string
{
    case MembershipForm = 'membership_form';    // Mitgliedschaftsantrag
    case Sepa = 'sepa';               // SEPA-Lastschriftmandat
    case Privacy = 'privacy';            // Datenschutzerklärung
    case IdDocument = 'id_document';        // Personalausweis / Reisepass
    case Other = 'other';              // Sonstiges

    // -------------------------------------------------------------------------
    // Labels
    // -------------------------------------------------------------------------

    public function label(string $locale = 'de'): string
    {
        return __('members.documents.category.'.$this->value);
    }

    // -------------------------------------------------------------------------
    // Erlaubte MIME-Types pro Kategorie
    // -------------------------------------------------------------------------

    /**
     * Manche Kategorien erlauben nur PDF (z.B. unterschriebene Verträge).
     * Ausweisdokumente dürfen auch als Bild hochgeladen werden.
     */
    public function allowedMimeTypes(): array
    {
        return match ($this) {
            self::MembershipForm,
            self::Sepa,
            self::Privacy => ['application/pdf'],
            self::IdDocument,
            self::Other => [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/tiff',
            ],
        };
    }

    public function allowedExtensions(): array
    {
        return match ($this) {
            self::MembershipForm,
            self::Sepa,
            self::Privacy => ['pdf'],
            self::IdDocument,
            self::Other => ['pdf', 'jpg', 'jpeg', 'png', 'tif', 'tiff'],
        };
    }

    // -------------------------------------------------------------------------
    // Hilfsmethoden
    // -------------------------------------------------------------------------

    /**
     * Für Blade-Selects: alle Kategorien als options-Array.
     * ['membership_form' => 'Mitgliedschaftsantrag', ...]
     */
    public static function selectOptions(string $locale = 'de'): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }

    /**
     * Validierungsstring für Laravel-Rules dynamisch bauen.
     * Wird im FormRequest genutzt.
     */
    public static function mimeTypesForValidation(): string
    {
        return collect(self::cases())
            ->flatMap(fn (self $case) => $case->allowedMimeTypes())
            ->unique()
            ->implode(',');
    }

    public static function extensionsForValidation(): string
    {
        return collect(self::cases())
            ->flatMap(fn (self $case) => $case->allowedExtensions())
            ->unique()
            ->implode(',');
    }

    /**
     * Prüft ob ein MIME-Type für diese Kategorie erlaubt ist.
     * Wird im Controller nach dem Upload zusätzlich geprüft.
     */
    public function isMimeTypeAllowed(string $mimeType): bool
    {
        return in_array($mimeType, $this->allowedMimeTypes(), strict: true);
    }
}
