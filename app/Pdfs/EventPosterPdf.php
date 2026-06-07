<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Models\Event\Event;
use App\Models\Event\EventTimeline;
use App\Models\Membership\Member;
use App\Services\QrCodeService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;
use TCPDF;

final class EventPosterPdf extends TCPDF
{
    private Event $event;

    private string $locale;

    private string $colorPrimary;

    private string $colorAccent;

    private string $colorText;

    private string $colorBg;

    private string $colorSecondary;

    private string $font = 'dejavusans';

    private bool $withImage;

    private string $textMode; // 'excerpt' | 'full'

    public function __construct(Event $event, string $locale = 'de', bool $withImage = true, string $textMode = 'excerpt')
    {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);

        $this->event = $event;
        $this->locale = $locale;
        $this->withImage = $withImage;
        $this->textMode = $textMode;

        // Load branding colors from settings (light mode)
        $colors = config('branding.colors.light');
        $this->colorPrimary = setting('branding.colors.light.primary') ?? $colors['primary'] ?? '#115E59';
        $this->colorAccent = setting('branding.colors.light.accent') ?? $colors['accent'] ?? '#0D9488';
        $this->colorSecondary = setting('branding.colors.light.secondary') ?? $colors['secondary'] ?? '#0E7490';
        $this->colorText = setting('branding.colors.light.text') ?? $colors['text'] ?? '#404040';
        $this->colorBg = setting('branding.colors.light.bg') ?? $colors['bg'] ?? '#FFFFFF';

        $this->SetCreator('CommuCore');
        $this->SetAuthor(setting('organization.name') ?? '');
        $this->SetTitle($event->title[$locale] ?? '');

        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetMargins(0, 0, 0);
        $this->SetAutoPageBreak(false, 0);
        $this->setCellHeightRatio(1.4);
    }

    /**
     * Parse a hex color string into [r, g, b] integers.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    private function hex(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    private function setFillHex(string $hex): void
    {
        [$r, $g, $b] = $this->hex($hex);
        $this->SetFillColor($r, $g, $b);
    }

    private function setTextHex(string $hex): void
    {
        [$r, $g, $b] = $this->hex($hex);
        $this->SetTextColor($r, $g, $b);
    }

    private function setDrawHex(string $hex): void
    {
        [$r, $g, $b] = $this->hex($hex);
        $this->SetDrawColor($r, $g, $b);
    }

    public function generateContent(): void
    {
        $this->AddPage();
        $this->drawBackground();
        $this->drawAccentBar();
        $this->drawTitle();
        $this->drawCoverImage();
        $this->drawExcerpt();
        $this->drawTimeline();
        $this->drawEventInfoBlock();
        $this->drawFooter();
    }

    // -------------------------------------------------------------------------
    // Background
    // -------------------------------------------------------------------------

    private function drawBackground(): void
    {
        $this->setFillHex($this->colorBg);
        $this->Rect(0, 0, 210, 297, 'F');

        // Subtle top accent strip
        $this->setFillHex($this->colorPrimary);
        $this->Rect(0, 0, 210, 3, 'F');
    }

    // -------------------------------------------------------------------------
    // Left accent bar
    // -------------------------------------------------------------------------

    private function drawAccentBar(): void
    {
        $this->setFillHex($this->colorAccent);
        $this->Rect(0, 3, 6, 294, 'F');
    }

    // -------------------------------------------------------------------------
    // Title
    // -------------------------------------------------------------------------

    private function drawTitle(): void
    {
        $title = $this->event->title[$this->locale] ?? '';

        $this->SetFont($this->font, 'B', 28);
        $this->setTextHex($this->colorPrimary);
        $this->SetXY(14, 14);
        $this->MultiCell(186, 12, $title, 0, 'L', false, 1);

        // Divider line under title
        $y = $this->GetY() + 3;
        $this->setDrawHex($this->colorAccent);
        $this->SetLineWidth(0.5);
        $this->Line(14, $y, 196, $y);
        $this->SetY($y + 4);
    }

    // -------------------------------------------------------------------------
    // Excerpt
    // -------------------------------------------------------------------------

    private function drawExcerpt(): void
    {
        $text = $this->textMode === 'full'
            ? strip_tags($this->event->description[$this->locale] ?? $this->event->excerpt[$this->locale] ?? '')
            : strip_tags($this->event->excerpt[$this->locale] ?? '');

        if (empty(trim($text))) {
            return;
        }

        $this->SetFont($this->font, '', 10);
        $this->setTextHex($this->colorText);
        $this->SetX(14);
        $this->MultiCell(186, 5, $text, 0, 'L', false, 1);
        $this->SetY($this->GetY() + 4);
    }

    // -------------------------------------------------------------------------
    // Timeline
    // -------------------------------------------------------------------------

    private function drawTimeline(): void
    {
        $timelines = $this->event->timelines()->orderBy('start', 'asc')->get();
        if ($timelines->isEmpty()) {
            return;
        }

        // Section heading
        $this->SetFont($this->font, 'B', 11);
        $this->setTextHex($this->colorPrimary);
        $this->SetX(14);
        $this->Cell(186, 7, __('event.timeline.heading', [], $this->locale), 0, 1, 'L');

        $this->SetY($this->GetY() + 1);

        /**
         * @var EventTimeline $item
         */
        foreach ($timelines as $item) {
            $startY = $this->GetY();

            // Time badge
            $timeStr = $item->start->format('H:i').' – '.$item->end->format('H:i');
            $this->setFillHex($this->colorPrimary);
            $this->setTextHex('#FFFFFF');
            $this->SetFont($this->font, 'B', 8);
            $this->SetXY(14, $startY);
            $this->Cell(28, 6, $timeStr, 0, 0, 'C', true);

            // Title
            $this->setTextHex($this->colorText);
            $this->SetFont($this->font, 'B', 10);
            $this->SetXY(44, $startY);
            $this->Cell(90, 6, $item->title_extern[$this->locale] ?? '', 0, 0, 'L');

            // Performer / Place
            $meta = [];
            if ($item->performer) {
                $meta[] = __('event.timeline.performer', [], $this->locale).': '.$item->performer;
            }
            if ($item->place) {
                $meta[] = __('event.timeline.place', [], $this->locale).': '.$item->place;
            }

            if (! empty($meta)) {
                $this->SetFont($this->font, '', 8);
                $this->setTextHex('#737373');
                $this->SetXY(44, $startY + 6);
                $this->MultiCell(152, 4, implode('  |  ', $meta), 0, 'L', false, 1);
            } else {
                $this->SetY($startY + 7);
            }

            $this->SetY($this->GetY() + 2);
        }

        $this->SetY($this->GetY() + 4);
    }

    // -------------------------------------------------------------------------
    // Cover image — hero, full width, fixed height, directly after title
    // -------------------------------------------------------------------------

    private function drawCoverImage(): void
    {
        if (! $this->withImage || ! $this->event->image) {
            return;
        }

        $imagePath = Storage::disk('public')->path('images/'.$this->event->image);

        if (! file_exists($imagePath)) {
            return;
        }

        $heroH = 55;
        $heroY = $this->GetY();
        $heroX = 14;
        $heroW = 182;

        $this->Image(
            file: $imagePath,
            x: $heroX,
            y: $heroY,
            w: $heroW,
            h: $heroH,
            fitbox: 'CM',
        );

        $this->SetY($heroY + $heroH + 5);
    }

    // -------------------------------------------------------------------------
    // Event info block (date / time / venue) — pinned above footer
    // -------------------------------------------------------------------------

    private function drawEventInfoBlock(): void
    {
        $blockY = 205;
        $blockH = 50;

        // Background
        $this->setFillHex($this->colorPrimary);
        $this->Rect(0, $blockY, 210, $blockH, 'F');

        // Left: big date
        $carbon = $this->event->event_date->locale($this->locale);
        $day = $carbon->isoFormat('Do');
        $month = $carbon->isoFormat('MMMM');
        $year = $carbon->isoFormat('YYYY');

        $this->setTextHex('#FFFFFF');
        $this->SetFont($this->font, 'B', 36);
        $this->SetXY(14, $blockY + 6);
        $this->Cell(45, 16, $day, 0, 1, 'L');

        $this->SetFont($this->font, '', 12);
        $this->SetXY(14, $blockY + 22);
        $this->Cell(45, 6, $month, 0, 1, 'L');

        $this->SetFont($this->font, '', 10);
        $this->SetXY(14, $blockY + 28);
        $this->Cell(45, 6, $year, 0, 1, 'L');

        // Divider
        $this->setDrawHex($this->colorAccent);
        $this->SetLineWidth(0.4);
        $this->Line(62, $blockY + 6, 62, $blockY + $blockH - 6);

        // Middle: time + venue
        $startTime = $this->event->start_time->format('H:i');
        $endTime = $this->event->end_time->format('H:i');

        $this->setTextHex('#FFFFFF');
        $this->SetFont($this->font, 'B', 18);
        $this->SetXY(68, $blockY + 6);
        $this->Cell(80, 10, $startTime.' – '.$endTime, 0, 1, 'L');

        $this->SetFont($this->font, 'B', 12);
        $this->SetXY(68, $blockY + 18);
        $this->Cell(80, 6, $this->event->venue->name ?? '', 0, 1, 'L');

        $this->SetFont($this->font, '', 9);
        $this->setTextHex('#99F6E4'); // teal-200 — lighter on dark bg
        $this->SetXY(68, $blockY + 25);
        $this->MultiCell(80, 4, $this->event->venue->address(false), 0, 'L', false, 1);
    }

    // -------------------------------------------------------------------------
    // Footer: logo + org info + QR code
    // -------------------------------------------------------------------------

    private function drawFooter(): void
    {
        $footerY = 258;

        // Thin accent line above footer
        $this->setDrawHex($this->colorAccent);
        $this->SetLineWidth(0.4);
        $this->Line(14, $footerY - 2, 196, $footerY - 2);

        // Logo
        $this->drawLogo($footerY);

        // Org info (center)
        $this->SetFont($this->font, 'B', 9);
        $this->setTextHex($this->colorPrimary);
        $this->SetXY(70, $footerY + 1);
        $this->Cell(80, 5, setting('organization.name') ?? '', 0, 1, 'C');

        $this->SetFont($this->font, '', 7);
        $this->setTextHex($this->colorText);
        $this->SetXY(70, $footerY + 7);
        $this->MultiCell(80, 3.5, $this->getOrgFooterLines(), 0, 'C', false, 1);

        // QR code (right)
        $this->drawQrCode($footerY);

        // Bottom strip
        $this->setFillHex($this->colorPrimary);
        $this->Rect(0, 291, 210, 6, 'F');
    }

    private function drawLogo(float $footerY): void
    {
        /** @var SettingsService $settingsService */
        $settingsService = app(SettingsService::class);
        $logoPath = $settingsService->getLogoPath();

        if ($logoPath && file_exists($logoPath)) {
            $this->Image($logoPath, 14, $footerY, 48, 28, '', '', '', true, 150, '', false, false, 0, 'CM');
        } else {
            // Fallback: organisation name as text logo
            $this->SetFont($this->font, 'B', 13);
            $this->setTextHex($this->colorSecondary);
            $this->SetXY(14, $footerY + 10);
            $this->Cell(48, 8, setting('organization.name') ?? '', 0, 0, 'C');
        }
    }

    private function drawQrCode(float $footerY): void
    {
        try {
            $slug = $this->event->slug[$this->locale] ?? $this->event->id;
            $url = config('app.url').'/events/'.$slug;

            /** @var QrCodeService $qrService */
            $qrService = app(QrCodeService::class);
            $svgString = $qrService->generateSvg($url, 80);

            // Write SVG to temp file so TCPDF can read it
            $tmpFile = tempnam(sys_get_temp_dir(), 'qr_').'_poster.svg';
            file_put_contents($tmpFile, $svgString);

            $this->ImageSVG(
                file: $tmpFile,
                x: 162,
                y: $footerY,
                w: 30,
                h: 30,
            );

            @unlink($tmpFile);
        } catch (\Throwable) {
            // QR generation failed — skip silently
        }
    }

    private function getOrgFooterLines(): string
    {
        $lines = [];

        $address = setting('organization.address');
        $zip = setting('organization.zip');
        $city = setting('organization.city');

        if ($address) {
            $lines[] = $address;
        }
        if ($zip || $city) {
            $lines[] = trim($zip.' '.$city);
        }

        $register = setting('organization.register_id');
        $court = setting('organization.court');
        if ($register || $court) {
            $lines[] = trim($register.' | '.$court, ' |');
        }

        $leaderBoard = Member::leaderBoardString();
        if ($leaderBoard) {
            $lines[] = $leaderBoard;
        }

        return implode("\n", $lines);
    }
}
