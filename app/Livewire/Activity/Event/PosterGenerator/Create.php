<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Event\PosterGenerator;

use App\Enums\Locale;
use App\Models\Event\Event;
use App\Pdfs\EventPosterPdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;

final class Create extends Component
{
    public ?Event $event = null;

    public ?string $imagePath = null;

    // Generation options
    public bool $withImage = true;

    public string $textMode = 'excerpt'; // 'excerpt' | 'full'

    public string $previewLocale = 'de';

    public function mount(?Event $event): void
    {
        $this->event = $event;
        $this->imagePath = null;
        $this->previewLocale = app()->getLocale();
    }

    //    public function generatePdf(): void
    //    {
    //        if (! $this->event) {
    //            return;
    //        }
    //
    //        foreach (Locale::toArray() as $locale) {
    //            $this->setOutputPath('pdf', $locale);
    //            $pdf = new EventPosterPdf($this->event, $locale, $this->withImage, $this->textMode);
    //            $pdf->generateContent();
    //            $pdf->Output($this->fullPath, 'F');
    //        }
    //
    //        session()->flash('message', 'PDF files generated successfully!');
    //
    //        $this->redirect(request()->header('Referer') ?? route('events.show', $this->event), navigate: true);
    //    }

    public function generatePosters(): void
    {
        if (! $this->event) {
            return;
        }

        foreach (Locale::toArray() as $locale) {
            // 1. Generate PDF first
            $this->setOutputPath('pdf', $locale);
            $pdf = new EventPosterPdf($this->event, $locale, $this->withImage, $this->textMode);
            $pdf->generateContent();
            $pdf->Output($this->fullPath, 'F');

            $pdfPath = $this->fullPath;

            // 2. Convert first page to JPEG via Imagick
            $this->setOutputPath('jpg', $locale);

            if (app()->isLocal()) {
                putenv('PATH='.getenv('PATH').':/opt/homebrew/bin:/usr/local/bin');
            }

            $imagick = new \Imagick;
            $imagick->setResolution(150, 150);
            $imagick->readImage($pdfPath.'[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->setImageCompressionQuality(90);
            $imagick->writeImage($this->fullPath);
            $imagick->destroy();
        }

        session()->flash('message', 'JPG files generated successfully!');

        $this->redirect(request()->header('Referer') ?? route('events.show', $this->event), navigate: true);
    }

    public function deletePoster(string $locale, string $type): void
    {
        if (! $this->event) {
            return;
        }

        $path = 'images/posters/'.$this->event->getFilename($locale).'.'.$type;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    private string $posterPath = '';

    private string $fullPath = '';

    private function setOutputPath(string $type, string $locale): void
    {
        $this->posterPath = 'images/posters/'.$this->event->getFilename($locale).'.'.$type;

        Storage::disk('public')->makeDirectory('images/posters');

        $this->fullPath = Storage::disk('public')->path($this->posterPath);

        $this->imagePath = Storage::disk('public')->exists($this->posterPath)
            ? Storage::disk('public')->url($this->posterPath)
            : null;
    }

    public function render(): View
    {
        return view('livewire.event.poster-generator.create');
    }
}
