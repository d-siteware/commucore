<?php

declare(strict_types=1);

use App\Http\Controllers\EventController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SecureImageController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\WhatsAppController;
use App\Livewire\App\Global\Mailinglist\Show;
use App\Livewire\App\Global\Mailinglist\Unsubscribe;
use App\Mail\SendMemberMassMail;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Services\EventReportService;
use App\Services\PdfGeneratorService;
use App\Services\QrCodeService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\App\Home\Page::class)
    ->name('home');

Route::get('/lang/{locale}', [LocaleController::class, 'switch'])
    ->name('locale.switch');

Route::get('/imprint', [StaticController::class, 'imprint'])
    ->name('imprint');

Route::get('/privacy', [StaticController::class, 'privacy'])
    ->name('privacy');

Route::get('/about-us', [StaticController::class, 'aboutUs'])
    ->name('about-us');

Route::get('/favicon.ico', function (SettingsService $settings) {
    $faviconInfo = $settings->getFaviconInfo();

    if ($faviconInfo['type'] === 'ico' && $faviconInfo['path']) {
        $file = Storage::disk('public')->get($faviconInfo['path']);

        return response($file, 200)
            ->header('Content-Type', 'image/x-icon')
            ->header('Cache-Control', 'public, max-age=31536000'); // 1 Jahr
    }

    // Fallback...
    if (file_exists(public_path('favicon.ico'))) {
        return response()->file(public_path('favicon.ico'), [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    abort(404);
})->name('favicon');

Route::get('/mailing-list/unsubscribe/{token}', Unsubscribe::class)
    ->name('mailing-list.unsubscribe');

Route::get('/mailing-list/{token}', Show::class)
    ->name('mailing-list.show');

Route::get('/rollback-email', [StaticController::class, 'rollbackMail'])
    ->name('rollback-email');

Route::get('/mitglied-werden', function () {
    return redirect()->route('members.application');
});

Route::prefix('members')
    ->name('members.')
    ->group(function (): void {
        Route::get('/application', \App\Livewire\Member\Apply\Page::class)
            ->name('application');

        Route::get('/print-member-application/{member}', [MembersController::class, 'printApplication'])
            ->name('print_application');

        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
            ->name('register');

        Route::post('/register', [RegisterController::class, 'create']);
    });

Route::prefix('events')
    ->name('events.')
    ->group(function (): void {
        Route::get('/subscription/confirm/{eventSubscription}/{token}', [EventController::class, 'confirmSubscription'])
            ->name('subscription.confirm');

        Route::get('/', [EventController::class, 'index'])
            ->name('index');

        Route::get('/{slug}', [EventController::class, 'show'])
            ->name('show');

        Route::get('/ics/{slug}', [EventController::class, 'generateIcs'])
            ->name('ics');
    });

Route::prefix('posts')
    ->name('posts.')
    ->group(function (): void {
        Route::get('/', [PostController::class, 'index'])
            ->name('index');

        Route::get('/{slug}', [PostController::class, 'show'])
            ->name('show');
    });

Route::prefix('chatter')
    ->name('chat.')
    ->group(function (): void {
        Route::get('/', [WhatsAppController::class, 'verify']);
        Route::post('/', [WhatsAppController::class, 'getMessage'])
            ->name('get-message');
        Route::post('/send', [WhatsAppController::class, 'sendMessage'])
            ->name('send');
    });

// TODO delete route if log entries do not show up after 3 months
Route::get('/dashboard', function () {
    \Illuminate\Support\Facades\Log::info('dashboard accessed from old route');

    return redirect()->route('dashboard');
})
    ->middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ]);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])
    ->prefix('backend')
    ->group(function (): void {
        Route::get('/tools', \App\Livewire\App\Tool\Index\Page::class)
            ->name('tools.index');

        Route::get('/members', \App\Livewire\Member\Index\Page::class)
            ->name('backend.members.index');

        Route::get('/members/create', \App\Livewire\Member\Create\Page::class)
            ->name('backend.members.create');

        Route::get('/members/import', \App\Livewire\Member\Import\Page::class)
            ->name('backend.members.import');

        Route::get('/members/roles', \App\Livewire\Member\Roles\Page::class)
            ->name('backend.members.roles');

        Route::get('/members/{member}', \App\Livewire\Member\Show\Page::class)
            ->name('backend.members.show');

        Route::get('/events', \App\Livewire\Event\Index\Page::class)
            ->name('backend.events.index');

        Route::get('/events/create', \App\Livewire\Event\Create\Page::class)
            ->name('backend.events.create');

        Route::get('/events/{event}', \App\Livewire\Event\Show\Page::class)
            ->name('backend.events.show');

        Route::get('/posts', \App\Livewire\Blog\Post\Index\Page::class)
            ->name('backend.posts.index');

        Route::get('/posts/create', \App\Livewire\Blog\Post\Create\Page::class)
            ->name('backend.posts.create');

        Route::get('/posts/{post}', \App\Livewire\Blog\Post\Show\Page::class)
            ->name('backend.posts.show');

        Route::get('/accounting', \App\Livewire\Accounting\Index\Page::class)
            ->name('accounting.index');

        Route::get('/transaction', \App\Livewire\Accounting\Transaction\Create\Page::class)
            ->name('transaction.create');

        Route::get('/transactions', \App\Livewire\Accounting\Transaction\Index\Page::class)
            ->name('transaction.index');

        Route::get('/account-report', \App\Livewire\Accounting\Report\Index\Page::class)->name('accounts.report.index');

        Route::get('/minutes', \App\Livewire\App\Tool\MeetingMinutes\Index::class)->name('minutes.index');

        Route::get('/minutes/create', \App\Livewire\App\Tool\MeetingMinutes\Create::class)->name('minutes.create');

        Route::get('/minutes/{meetingMinute}/edit', \App\Livewire\App\Tool\MeetingMinutes\Edit::class)->name('minutes.edit');

        //
        //        Route::get('/events/report/{event}', function (Event $event, EventReportService $reportService) {
        //            return $reportService->generate($event);
        //        })->name('backend.events.report');

        Route::get('/events/report/{event}', function (Event $event) {
            $pdfContent = PdfGeneratorService::generatePdf('event-report', $event, null, true);

            return response($pdfContent)->header('Content-Type', 'application/pdf');
        })
            ->name('backend.events.report');

        //        Route::get('/account-report/print/{account_report}', function (\App\Models\Accounting\AccountReport $accountReport, \App\Services\AccountReportService $reportService) {
        //            return $reportService->generate($accountReport);
        //        })->name('accounts.report.print');

        Route::get('/account-report/print/{account_report}', function (AccountReport $accountReport) {
            $pdfContent = PdfGeneratorService::generatePdf('account-report', $accountReport, null, true);

            return response($pdfContent)->header('Content-Type', 'application/pdf');
        })
            ->name('accounts.report.print');

        //        Route::get('/transaction/invoice/preview/{transaction}', function (Transaction $transaction, \App\Services\MemberInvoiceService $invoiceService) {
        //            $member = $transaction->member_transaction->member ?? null;
        //            $pdfContent = $invoiceService->generate($transaction, $member, app()->getLocale());
        //
        //            return response($pdfContent)
        //                ->header('Content-Type', 'application/pdf')
        //                ->header('Content-Disposition', 'inline; filename="Rechnung_'.$transaction->id.'.pdf"');
        //        })->name('transaction.invoice.preview');

        Route::get('/transaction/invoice/preview/{transaction}', function (Transaction $transaction) {
            $member = $transaction->member_transaction->member ?? null;
            $pdfContent = PdfGeneratorService::generatePdf('invoice', ['transaction' => $transaction, 'member' => $member], null, true);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline; filename=\"Rechnung_{$transaction->id}.pdf\"");
        })
            ->name('transaction.invoice.preview');

        Route::get('/account-report/audit/{account_report_audit}', function (AccountReportAudit $accountReportAudit) {
            if (Auth::user()->id === $accountReportAudit->user_id) {
                return view('accounts.reports.audit', ['accountReportAuditId' => $accountReportAudit->id]);
            } else {
                return redirect()->route('accounts.report.index');
            }
        })
            ->name('account-report.audit');

        Route::get('/accounts', \App\Livewire\Accounting\Account\Index\Page::class)
            ->name('accounts.index');

        Route::get('/receipts', App\Livewire\Accounting\Receipt\Index\Page::class)
            ->name('receipts.index');

        Route::get('/dashboard', function () {
            return view('dashboard');
        })
            ->name('dashboard');

        Route::get('/test-mail-preview', function () {
            $mailable = new SendMemberMassMail('Daniel', request('subject'), request('message'), request('locale'), request('url'), request('url_label'), []);

            return $mailable->render();
        })
            ->name('test-mail-preview');

        Route::get('/receipt-file/{filename}', function ($filename) {
            $path = storage_path('app/private/accounting/receipts/'.$filename);
            if (! file_exists($path)) {
                abort(404);
            }

            // Optional: hier Auth-Checks einbauen, damit die Datei nur berechtigte Nutzer sehen

            return response()->file($path);
        })
            ->name('receipt.file');

        //        Route::get('/secure-image/{filename}', [SecureImageController::class, 'show'])
        //            ->where('filename', '.*')
        //            ->name('secure-image.preview');

        Route::get('/secure-image/{category}/{filename}', [SecureImageController::class, 'show'])
            ->where('category', '[a-zA-Z0-9\-_/]+')
            ->where('filename', '[^/]+')
            ->name('secure-image.category');

        Route::get('/secure-download/{filename}', [SecureImageController::class, 'download'])
            ->where('filename', '.*')
            ->name('secure-download');

        //        Route::get('/secure-image/{filename}', function (Request $request, $filename) {
        //
        // //            abort_unless(auth()->check(), 403);
        // //            $path = storage_path("app/private/accounting/receipts/previews/{$filename}.png");
        // //            abort_unless(Storage::disk('local')->exists($path), 404);
        // //
        // //            return response()->file($path, ['Content-Type' => 'image/png']);
        //
        //        });

        Route::get('/shared-images/index', \App\Livewire\App\Tool\SharedImage\Index\Page::class)
            ->name('shared-image.index');
        Route::get('/shared-images/create', \App\Livewire\App\Tool\SharedImage\Create\Page::class)
            ->name('shared-image.create');

        //        Route::get('/secure-image/shared-images/thumbs/{filename}', [SecureImageController::class, 'show'])
        //            ->where('filename', '.*')
        //            ->name('secure-image.shared-thumb');

        Route::get('/branding', \App\Livewire\App\Branding\Page::class)->name('branding');

    }); // End middleware auth, jetstream, verified, group

Route::redirect('/feed', '/api/feed/events', 301);

/**
 *   Routes for testing, subject to future deletion
 */
if (app()->isLocal()) {
    Route::get('/mailer-test', [TestingController::class, 'mailTest'])
        ->name('mail-tester');

    Route::get('/poster/preview/{event}', function ($eventId) {
        // Fetch the event (or use a dummy one for testing)
        $event = Event::findOrFail($eventId); // Replace with your model logic
        $imagePath = null; // Optional: Add a sample image path if needed

        // Render the same view as Browsershot
        return view('event_posters.main_jpeg', [
            'event' => $event,
            'imagePath' => $imagePath,
        ]);
    })
        ->name('poster.preview.jpg');

    Route::get('/poster/preview_pdf/{event}', function ($eventId) {
        // Fetch the event (or use a dummy one for testing)
        $event = Event::findOrFail($eventId); // Replace with your model logic
        $imagePath = null; // Optional: Add a sample image path if needed
        $qrService = new QrCodeService;

        $qrCode = $qrService->generateSvg(config('app.url').'/'.$event->slug['de'], 80);

        // Render the same view as Browsershot
        return view('event_posters.main_pdf', [
            'event' => $event,
            'imagePath' => $imagePath,
            'qrcode' => $qrCode,
            'dpi' => 300,
        ]);
    })
        ->name('poster.preview.pdf');

    Route::get('/pdf-preview/event-invitation/{event}', function (\App\Models\Event\Event $event) {
        abort_unless(auth()->check(), 403);

        $pdf = \App\Services\PdfGeneratorService::generatePdf('event-invitation-letter', $event);
        $filename = 'preview-event-invitation.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    })->name('pdf.preview.event-invitation');

    Route::get('/pdf-preview/event-program', function () {
        abort_unless(auth()->check(), 403);
        $filename = 'preview-event-program.pdf';
        $pdf = \App\Services\PdfGeneratorService::generatePdf('event-programm-letter', [], $filename);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    })->name('pdf.preview.event-program');
}
