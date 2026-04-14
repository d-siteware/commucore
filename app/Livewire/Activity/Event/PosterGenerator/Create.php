<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Event\PosterGenerator;

use App\Enums\Locale;
use App\Models\Event\Event;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

final class Create extends Component
{
    public $event;

    protected $posterPath;

    protected $fullPath;

    public $imagePath;

    public function mount(?Event $event): void
    {
        $this->event = $event;
        $this->imagePath = '';
    }

    /**
     * @throws \Throwable
     * @throws CouldNotTakeBrowsershot
     */
    public function generateJpeg(): void
    {

        foreach (Locale::toArray() as $value) {

            $htmlContent = view('event_posters.main_jpeg', ['event' => $this->event, 'imagePath' => $this->imagePath, 'locale' => $value])->render();

            $this->setImagePath('jpg', $value);

            $this->makeImage($htmlContent)->windowSize(1280, 960)
                ->format('jpeg')
                ->save($this->fullPath);

            session()->flash('message', 'JPG files generated successfully!');

        }
    }

    public function generatePdf(): void
    {
        foreach (Locale::toArray() as $value) {

            $qrService = new QrCodeService;

            $qrcode = $qrService->generateSvg(config('app.url').'/events/'.$this->event->slug[$value], 120);

            $htmlContent = view('event_posters.main_pdf', ['event' => $this->event, 'imagePath' => $this->imagePath, 'locale' => $value, 'qrcode' => $qrcode, 'dpi' => 96])->render();

            $this->setImagePath('pdf', $value);

            $this->makeImage($htmlContent)
                ->format('A4')
                ->fullPage()
                ->save($this->fullPath);

            session()->flash('message', 'PDF generated successfully!');
        }
    }

    protected function makeImage(string $htmlContent): Browsershot
    {

        $nodeBinary = ! app()->isLocal()
            ? '/usr/bin/node'
            : '/Users/daniel.kortvelyessy/Library/Application Support/Herd/config/nvm/versions/node/v22.14.0/bin/node';

        $npmBinary = ! app()->isLocal()
            ? '/usr/bin/npm'
            : '/Users/daniel.kortvelyessy/Library/Application Support/Herd/config/nvm/versions/node/v22.14.0/bin/npm';

        $includePath = ! app()->isLocal()
            ? '/usr/bin'
            : '/Users/daniel.kortvelyessy/Library/Application Support/Herd/config/nvm/versions/node/v22.14.0/bin';

        \Log::info('Configuring Browsershot with node: '.$nodeBinary.', npm: '.$npmBinary.', includePath: '.$includePath);

        $browserShot = Browsershot::html($htmlContent)
            ->setNodeBinary($nodeBinary)
            ->setNpmBinary($npmBinary)
            ->setIncludePath($includePath)
            ->noSandbox()
            ->setOption('args', [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--disable-crash-reporter',
                '--no-crash-upload',
                '--disable-extensions',
                '--disable-sync',
                '--disable-background-networking',
            ])
            ->setOption('env', [
                'HOME' => '/tmp',
                'TMPDIR' => '/tmp',
                'LD_LIBRARY_PATH' => '',
            ]);

        if (!app()->isLocal()) {
            $chromePath = glob('/var/source/node_modules/puppeteer/.local-chromium/chrome/linux_arm-*/chrome-linux64/chrome')[0] ?? null;
            \Log::info('Attempting to use Chrome path: '.($chromePath ?? 'none'));
            if (! $chromePath || ! file_exists($chromePath)) {
                \Log::error('Chromium not found. Run: PUPPETEER_CACHE_DIR=/var/source/node_modules/puppeteer/.local-chromium npx puppeteer browsers install chrome');
            }

            return $browserShot->setChromePath($chromePath);
        }

        return $browserShot;
    }

    protected function setImagePath(string $type = 'jpg', $locale = 'de'): void
    {

        $this->posterPath = 'images/posters/'.$this->event->getFilename($locale).'.'.$type;

        // Ensure the directory exists
        Storage::disk('public')
            ->makeDirectory('images/posters');

        // Full path for Browsershot
        $this->fullPath = Storage::disk('public')
            ->path($this->posterPath);

        $this->imagePath = Storage::disk('public')->exists($this->fullPath)
        ? Storage::disk('public')->url($this->fullPath)
        : null;
    }

    public function render(): View
    {
        return view('livewire.event.poster-generator.create');
    }
}
