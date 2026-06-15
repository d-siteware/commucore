<?php

declare(strict_types=1);

use App\Enums\MemberExportType;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MemberExportController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SecureImageController;
use App\Http\Controllers\StaticController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\WhatsAppController;
use App\Livewire\App\Global\Mailinglist\Show;
use App\Livewire\App\Global\Mailinglist\Unsubscribe;
use App\Livewire\App\Home\Page;
use App\Livewire\App\Tool\MeetingMinutes\Create;
use App\Livewire\App\Tool\MeetingMinutes\Edit;
use App\Livewire\Member\Export\Form;
use App\Livewire\Member\Fees\Index;
use App\Mail\SendMemberMassMail;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use App\Pdfs\EventPosterPdf;
use App\Services\Accounting\AnnualReportService;
use App\Services\Import\MemberFieldMapper;
use App\Services\Import\MemberImportBackup;
use App\Services\PdfGeneratorService;
use App\Services\QrCodeService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', Page::class)->name('home');

Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/imprint', [StaticController::class, 'imprint'])->name('imprint');
Route::get('/privacy', [StaticController::class, 'privacy'])->name('privacy');
Route::get('/about-us', [StaticController::class, 'aboutUs'])->name('about-us');
Route::get('/rollback-email', [StaticController::class, 'rollbackMail'])->name('rollback-email');

Route::get('/favicon.ico', function (SettingsService $settings) {
    $faviconInfo = $settings->getFaviconInfo();

    if ($faviconInfo['type'] === 'ico' && $faviconInfo['path']) {
        $file = Storage::disk('public')->get($faviconInfo['path']);

        return response($file, 200)
            ->header('Content-Type', 'image/x-icon')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    if (file_exists(public_path('favicon.ico'))) {
        return response()->file(public_path('favicon.ico'), [
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    abort(404);
})->name('favicon');

/*
|--------------------------------------------------------------------------
| SSO (Single Sign-On)
|--------------------------------------------------------------------------
| Token wird von app.commu-core.app generiert und hier eingelöst.
| Kein Auth-Middleware – das Token ist die Authentifizierung.
*/

Route::get('/auth/sso', [SsoController::class, 'login'])
    ->name('sso.login')
    ->middleware('guest');

/*
|--------------------------------------------------------------------------
| Onboarding
|--------------------------------------------------------------------------
| Nur, wenn eine neue Installation erfolgt.
| Für Instanzen von *.commu-core.app
*/
Route::get('/onboarding', App\Livewire\App\Onboarding\Page::class)
    ->middleware('auth')
    ->name('onboarding');

/*
|--------------------------------------------------------------------------
| Mailing List
|--------------------------------------------------------------------------
*/

Route::get('/mailing-list/unsubscribe/{token}', Unsubscribe::class)->name('mailing-list.unsubscribe');
Route::get('/mailing-list/{token}', Show::class)->name('mailing-list.show');

/*
|--------------------------------------------------------------------------
| Members (Public)
|--------------------------------------------------------------------------
*/

Route::get('/mitglied-werden', fn () => redirect()->route('members.application'));

Route::prefix('members')->name('members.')->group(function (): void {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'create']);
    Route::get('/application', App\Livewire\Member\Apply\Page::class)->name('application');
    Route::get('/application/verify', App\Livewire\Member\Apply\Page::class)->name('application.verify');
    Route::get('/print-member-application/{member}', [MembersController::class, 'printApplication'])->name('print_application');
});

/*
|--------------------------------------------------------------------------
| Events (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('events')->name('events.')->group(function (): void {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
    Route::get('/ics/{slug}', [EventController::class, 'generateIcs'])->name('ics');
    Route::get('/subscription/confirm/{eventSubscription}/{token}', [EventController::class, 'confirmSubscription'])->name('subscription.confirm');
});

/*
|--------------------------------------------------------------------------
| Posts (Public)
|--------------------------------------------------------------------------
*/

Route::prefix('posts')->name('posts.')->group(function (): void {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| WhatsApp / Chatter
|--------------------------------------------------------------------------
*/

Route::prefix('chatter')->name('chat.')->group(function (): void {
    Route::get('/', [WhatsAppController::class, 'verify']);
    Route::post('/', [WhatsAppController::class, 'getMessage'])->name('get-message');
    Route::post('/send', [WhatsAppController::class, 'sendMessage'])->name('send');
});

/*
|--------------------------------------------------------------------------
| Redirects
|--------------------------------------------------------------------------
*/

Route::redirect('/feed', '/api/feed/events', 301);
Route::redirect('/mitglied-werden', '/members/application', 301);

// Legacy dashboard route
Route::get('/dashboard', function () {
    Log::info('dashboard accessed from old route');

    return redirect()->route('dashboard');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified']);

/*
|--------------------------------------------------------------------------
| Backend (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('backend')
    ->group(function (): void {

        Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

        // Members
        Route::prefix('members')->name('backend.members.')->group(function (): void {
            Route::get('/', App\Livewire\Member\Index\Page::class)->name('index');
            Route::get('/create', App\Livewire\Member\Create\Page::class)->name('create');
            Route::get('/import', App\Livewire\Member\Import\Page::class)->name('import');
            Route::get('/export', Form::class)->name('export');
            Route::get('/roles', App\Livewire\Member\Roles\Page::class)->name('roles');
            Route::get('/fees', Index::class)->name('fees');
            Route::get('/{member}', App\Livewire\Member\Show\Page::class)->name('show');

            Route::get('/export/download', MemberExportController::class)->name('export.download');

            Route::get('/import/template', function (Request $request): StreamedResponse {
                Gate::authorize('export', Member::class);

                $type = MemberExportType::tryFrom($request->query('type', ''))
                    ?? MemberExportType::STAMMDATEN;

                $fields = match ($type) {
                    MemberExportType::STAMMDATEN => array_intersect_key(
                        MemberFieldMapper::MEMBER_FIELDS,
                        array_flip(['name', 'first_name', 'email', 'phone', 'mobile', 'address', 'zip', 'city', 'country', 'locale', 'gender']),
                    ),
                    default => MemberFieldMapper::MEMBER_FIELDS,
                };

                return response()->streamDownload(function () use ($fields): void {
                    $handle = fopen('php://output', 'w');
                    if ($handle === false) {
                        return;
                    }
                    fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM für Excel
                    fputcsv($handle, array_values($fields), ';');
                    fclose($handle);
                }, 'commucore_import_vorlage_'.$type->value.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
            })->name('import.template');
        });

        // Import backup download (außerhalb members prefix wegen URL-Struktur)
        Route::get('/import/backup', function (Request $request): StreamedResponse {
            $path = decrypt($request->query('path'));
            if (! Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                abort(404);
            }
            if (! MemberImportBackup::isRollbackAllowed($path)) {
                abort(410);
            }

            return Illuminate\Support\Facades\Storage::disk('local')->download(
                $path, 'commucore_backup_'.now()->format('Y-m-d_His').'.json'
            );
        })->name('import.backup-download');

        // Events
        Route::prefix('events')->name('backend.events.')->group(function (): void {
            Route::get('/', App\Livewire\Activity\Event\Index\Page::class)->name('index');
            Route::get('/create', App\Livewire\Activity\Event\Create\Page::class)->name('create');
            Route::get('/{event}', App\Livewire\Activity\Event\Show\Page::class)->name('show');
            Route::get('/report/{event}', function (Event $event) {
                $pdfContent = PdfGeneratorService::generatePdf('event-report', $event, null, true);

                return response($pdfContent)->header('Content-Type', 'application/pdf');
            })->name('report');

            Route::get('/{event}/poster/preview/{locale}', function (
                Event $event,
                string $locale = 'de',
            ) {
                abort_unless(
                    auth()->user()?->can('update', $event),
                    403
                );

                $withImage = (bool) request()->integer('image', 1);
                $textMode = in_array(request()->string('text')->toString(), ['excerpt', 'full'])
                    ? request()->string('text')->toString()
                    : 'excerpt';

                $pdf = new EventPosterPdf($event, $locale, $withImage, $textMode);
                $pdf->generateContent();

                return response($pdf->Output('preview.pdf', 'S'))
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="poster-preview.pdf"');
            })->name('poster.preview')->middleware(['auth', 'verified']);
        });

        // Posts / Blog
        Route::prefix('posts')->name('backend.posts.')->group(function (): void {
            Route::get('/', App\Livewire\Activity\Blog\Post\Index\Page::class)->name('index');
            Route::get('/create', App\Livewire\Activity\Blog\Post\Create::class)->name('create');
            Route::get('/{post}', App\Livewire\Activity\Blog\Post\Show\Page::class)->name('show');
        });

        // Accounting
        Route::prefix('')->name('')->group(function (): void {
            Route::get('/accounting', App\Livewire\Accounting\Index\Page::class)->name('accounting.index');
            Route::get('/transaction', App\Livewire\Accounting\Transaction\Create\Page::class)->name('transaction.create');
            Route::get('/transactions', App\Livewire\Accounting\Transaction\Index\Page::class)->name('transaction.index');
            Route::get('/accounts', App\Livewire\Accounting\Account\Index\Page::class)->name('accounts.index');
            Route::get('/accounts/create', App\Livewire\Accounting\Account\Create\Page::class)->name('accounts.create');
            Route::get('/account-report', App\Livewire\Accounting\Report\Index\Page::class)->name('accounts.report.index');

            Route::get('/receipts', App\Livewire\Accounting\Receipt\Index\Page::class)->name('receipts.index');
            Route::get('/sepa-collections', App\Livewire\Accounting\SepaCollection\Index\Page::class)->name('sepa-collections.index');

            Route::get('/account-report/print/{account_report}', function (AccountReport $accountReport) {
                $pdfContent = PdfGeneratorService::generatePdf('account-report', $accountReport, null, true);

                return response($pdfContent)->header('Content-Type', 'application/pdf');
            })->name('accounts.report.print');

            Route::get('/account-report/audit/{account_report_audit}', function (AccountReportAudit $accountReportAudit) {
                if (Auth::user()->id === $accountReportAudit->user_id) {
                    return view('accounts.reports.audit', ['accountReportAuditId' => $accountReportAudit->id]);
                }

                return redirect()->route('accounts.report.index');
            })->name('account-report.audit');

            Route::get('/transaction/invoice/preview/{transaction}', function (Transaction $transaction) {
                $member = $transaction->member_transaction->member ?? null;
                $pdfContent = PdfGeneratorService::generatePdf('invoice', ['transaction' => $transaction, 'member' => $member], null, true);

                return response($pdfContent)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', "inline; filename=\"Rechnung_{$transaction->id}.pdf\"");
            })->name('transaction.invoice.preview');

            Route::get('/fiscal-years', App\Livewire\Accounting\FiscalYear\Index\Page::class)
                ->name('fiscal-years.index')
                ->can('create', Account::class);

            Route::get('/fiscal-years/{year}/close', App\Livewire\Accounting\FiscalYear\Close\Page::class)
                ->name('fiscal-years.close')
                ->can('close', FiscalYear::class);
        });

        // Funding & Projects
        Route::prefix('funding')->name('funding.')->group(function (): void {
            Route::get('/', App\Livewire\Accounting\Funding\Index\Page::class)->name('index');
            Route::get('/create', App\Livewire\Accounting\Funding\Create\Page::class)->name('create');
            Route::get('/{funding}', App\Livewire\Accounting\Funding\Show\Page::class)->name('show');
        });

        Route::prefix('project')->name('project.')->group(function (): void {
            Route::get('/', App\Livewire\Activity\Project\Index\Page::class)->name('index');
            Route::get('/create', App\Livewire\Activity\Project\Create\Page::class)->name('create');
            Route::get('/{project}', App\Livewire\Activity\Project\Show\Page::class)->name('show');
        });

        // Tools
        Route::prefix('tools')->group(function (): void {
            Route::get('/mailing', App\Livewire\App\Tool\Mailing\Page::class)->name('backend.tools.mailing');
        });

        // Minutes
        Route::prefix('minutes')->name('minutes.')->group(function (): void {
            Route::get('/', App\Livewire\App\Tool\MeetingMinutes\Index::class)->name('index');
            Route::get('/create', Create::class)->name('create');
            Route::get('/{meetingMinute}/edit', Edit::class)->name('edit');
        });

        // Shared Images
        Route::prefix('shared-images')->name('shared-image.')->group(function (): void {
            Route::get('/index', App\Livewire\App\Tool\SharedImage\Index\Page::class)->name('index');
            Route::get('/create', App\Livewire\App\Tool\SharedImage\Create\Page::class)->name('create');
        });

        // Notifications
        Route::post('/notifications/{id}/read', function (string $id) {
            $user = Auth::user();
            if ($user === null) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            if ($id === 'all') {
                $user->unreadNotifications->markAsRead();
            } else {
                $notification = $user->notifications()->find($id);
                if ($notification instanceof DatabaseNotification) {
                    $notification->markAsRead();
                }
            }

            return response()->json(['success' => true]);
        })->name('notifications.markAsRead');

        // Files & Documents
        Route::get('/receipt-file/{filename}', function ($filename) {
            $path = storage_path('app/private/accounting/receipts/'.$filename);
            if (! file_exists($path)) {
                abort(404);
            }

            return response()->file($path);
        })->name('receipt.file');

        Route::get('/secure-image/{category}/{filename}', [SecureImageController::class, 'show'])
            ->where('category', '[a-zA-Z0-9\-_/]+')
            ->where('filename', '[^/]+')
            ->name('secure-image.category');

        Route::get('/secure-download/{filename}', [SecureImageController::class, 'download'])
            ->where('filename', '.*')
            ->name('secure-download');

        Route::get('/documents/{uuid}/download', [DocumentController::class, 'download'])->name('document.download');
        Route::get('/documents/{uuid}/preview', [DocumentController::class, 'preview'])->name('document.preview');

        // Settings
        Route::get('/settings', App\Livewire\App\Branding\Page::class)->name('settings');

        // Dev tools
        Route::get('/test-mail-preview', function () {
            $mailable = new SendMemberMassMail('Daniel', request('subject'), request('message'), request('locale'), request('url'), request('url_label'), []);

            return $mailable->render();
        })->name('test-mail-preview');

    }); // End backend group

/*
|--------------------------------------------------------------------------
| Local / Testing only
|--------------------------------------------------------------------------
*/

if (app()->isLocal()) {
    Route::get('/mailer-test', [TestingController::class, 'mailTest'])->name('mail-tester');

    Route::get('/poster/preview/{event}', function ($eventId) {
        $event = Event::findOrFail($eventId);

        return view('event_posters.main_jpeg', ['event' => $event, 'imagePath' => null]);
    })->name('poster.preview.jpg');

    Route::get('/poster/preview_pdf/{event}', function ($eventId) {
        $event = Event::findOrFail($eventId);
        $qrCode = (new QrCodeService)->generateSvg(config('app.url').'/'.$event->slug['de'], 80);

        return view('event_posters.main_pdf', ['event' => $event, 'imagePath' => null, 'qrcode' => $qrCode, 'dpi' => 300]);
    })->name('poster.preview.pdf');

    Route::get('/pdf-preview/event-invitation/{event}', function (Event $event) {
        abort_unless(auth()->check(), 403);
        $pdf = PdfGeneratorService::generatePdf('event-invitation-letter', $event);

        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="preview-event-invitation.pdf"']);
    })->name('pdf.preview.event-invitation');

    Route::get('/pdf-preview/annualreport/{fiscalyear:year}', function (FiscalYear $fiscalyear) {
        abort_unless(auth()->check(), 403);

        $data = (new AnnualReportService)->build($fiscalyear->year);

        $pdf = PdfGeneratorService::generatePdf('annual-report', [
            'year' => $data['year'],
            'snapshot' => $data['snapshot'],
            'transactions' => $data['transactions'],
        ]);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview-annual-report.pdf"',
        ]);
    })->name('pdf.preview.annual-report');

    Route::get('/pdf-preview/event-program', function () {
        abort_unless(auth()->check(), 403);
        $pdf = PdfGeneratorService::generatePdf('event-programm-letter', [], 'preview-event-program.pdf');

        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="preview-event-program.pdf"']);
    })->name('pdf.preview.event-program');
}
