<?php

declare(strict_types=1);

use App\Helpers\DateHelper;
use App\Models\Locale;
use Carbon\Carbon;

// ──────────────────────────────────────────────────────────────────────────
// Locale::formatDate
// ──────────────────────────────────────────────────────────────────────────

describe('Locale::formatDate', function (): void {

    it('formats dates with de locale format', function (): void {
        $locale = new Locale([
            'name' => 'de',
            'date_format' => 'DD.MM.JJJJ',
        ]);
        $date = Carbon::create(2026, 6, 19);

        expect($locale->formatDate($date))->toBe('19.06.2026');
    });

    it('formats dates with hu locale format', function (): void {
        $locale = new Locale([
            'name' => 'hu',
            'date_format' => 'JJJJ.MM.DD.',
        ]);
        $date = Carbon::create(2026, 6, 19);

        expect($locale->formatDate($date))->toBe('2026.06.19.');
    });

    it('formats dates with en locale format', function (): void {
        $locale = new Locale([
            'name' => 'en',
            'date_format' => 'MM/DD/JJJJ',
        ]);
        $date = Carbon::create(2026, 6, 19);

        expect($locale->formatDate($date))->toBe('06/19/2026');
    });

    it('falls back to DD.MM.JJJJ when date_format is null', function (): void {
        $locale = new Locale(['name' => 'xx']);
        $date = Carbon::create(2026, 6, 19);

        expect($locale->formatDate($date))->toBe('19.06.2026');
    });

});

// ──────────────────────────────────────────────────────────────────────────
// Locale::formatTime
// ──────────────────────────────────────────────────────────────────────────

describe('Locale::formatTime', function (): void {

    it('formats time as H:i', function (): void {
        $locale = new Locale(['name' => 'de']);
        $date = Carbon::create(2026, 6, 19, 14, 30, 0);

        expect($locale->formatTime($date))->toBe('14:30');
    });

});

// ──────────────────────────────────────────────────────────────────────────
// Locale::formatName
// ──────────────────────────────────────────────────────────────────────────

describe('Locale::formatName', function (): void {

    it('formats name as first last for first_last order', function (): void {
        $locale = new Locale(['name' => 'en', 'name_order' => 'first_last']);

        expect($locale->formatName('John', 'Doe'))->toBe('John Doe');
    });

    it('formats name as last, first for last_first order', function (): void {
        $locale = new Locale(['name' => 'de', 'name_order' => 'last_first']);

        expect($locale->formatName('John', 'Doe'))->toBe('Doe, John');
    });

    it('defaults to first last when name_order is null', function (): void {
        $locale = new Locale(['name' => 'xx']);

        expect($locale->formatName('John', 'Doe'))->toBe('John Doe');
    });

});

// ──────────────────────────────────────────────────────────────────────────
// DateHelper::formatDate
// ──────────────────────────────────────────────────────────────────────────

describe('DateHelper::formatDate', function (): void {

    it('returns empty string for null date', function (): void {
        expect(DateHelper::formatDate(null))->toBe('');
    });

    it('formats a date using the fallback when no locale service is available', function (): void {
        $date = Carbon::create(2026, 6, 19);
        $result = DateHelper::formatDate($date);

        // In unit test context, LocaleService throws RuntimeException
        // so it falls back to d.m.Y
        expect($result)->toBe('19.06.2026');
    });

});

// ──────────────────────────────────────────────────────────────────────────
// DateHelper::formatDateTime
// ──────────────────────────────────────────────────────────────────────────

describe('DateHelper::formatDateTime', function (): void {

    it('returns empty string for null date', function (): void {
        expect(DateHelper::formatDateTime(null))->toBe('');
    });

    it('formats date and time', function (): void {
        $date = Carbon::create(2026, 6, 19, 14, 30, 0);
        $result = DateHelper::formatDateTime($date);

        // In unit test context, falls back to d.m.Y H:i
        expect($result)->toBe('19.06.2026 14:30');
    });

});

// ──────────────────────────────────────────────────────────────────────────
// DateHelper::formatDateForLocale
// ──────────────────────────────────────────────────────────────────────────

describe('DateHelper::formatDateForLocale', function (): void {

    it('formats a date using a specific locale', function (): void {
        $locale = new Locale([
            'name' => 'hu',
            'date_format' => 'JJJJ.MM.DD.',
        ]);
        $date = Carbon::create(2026, 6, 19);

        expect(DateHelper::formatDateForLocale($date, $locale))->toBe('2026.06.19.');
    });

    it('returns empty string for null date', function (): void {
        $locale = new Locale(['name' => 'de']);

        expect(DateHelper::formatDateForLocale(null, $locale))->toBe('');
    });

});
