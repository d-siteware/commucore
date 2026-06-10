<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\Mailing;

use App\Jobs\DeleteEmailAttachments;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Livewire\Traits\Sortable;
use App\Mail\SendMemberMassMail;
use App\Models\Locale;
use App\Models\MailingHistory;
use App\Models\MailingList;
use App\Models\Membership\Member;
use Carbon\Carbon;
use Exception;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

final class Page extends Component
{
    use HasPrivileges;
    use PersistsTabs;
    use Sortable;
    use WithFileUploads;
    use WithPagination;

    public string $selectedTab = 'create-mail-tab';

    public array $subject;

    public array $message;

    public array $attachments;

    public bool $include_mailing_list = false;

    public bool $target_type;

    public array $monthlySubscriptions = [];

    public array $yearlySubscriptions = [];

    public int $totalSubscriptionsThisYear;

    public ?array $urlLabel;

    public ?string $url = '';

    public bool $setLink = false;

    public bool $setAttachment = false;

    public bool $setPersonalGreeting = true;

    public array $activeLocales = [];

    #[Computed]
    public function mailingList(): LengthAwarePaginator
    {
        return MailingList::query()
            ->whereNotNull('verified_at')
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    protected function subscriptionCurrentMonth(): array
    {
        return DB::table('mailing_lists')
            ->selectRaw('DATE(verified_at) as date, COUNT(*) as visitors')
            ->whereBetween('verified_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('date')
            ->get()
            ->map(fn ($item): array => ['date' => $item->date, 'visitors' => $item->visitors])
            ->toArray();
    }

    protected function subscriptionCurrentYear(): array
    {
        return DB::table('mailing_lists')
            ->selectRaw('strftime("%m", verified_at) as month, COUNT(*) as visitors')
            ->whereYear('verified_at', Carbon::now('Europe/Berlin')->year)
            ->groupBy('month')
            ->orderByRaw('month ASC')
            ->get()
            ->map(fn ($item): array => ['month' => $item->month, 'visitors' => $item->visitors])
            ->toArray();
    }

    public function totalSubscriptionCurrentYear(): int
    {
        return DB::table('mailing_lists')
            ->whereYear('verified_at', Carbon::now('Europe/Berlin')->year)
            ->count();
    }

    public function sendMembersMail(): void
    {
        $this->checkPrivilege(MailingList::class);
        $this->validate();

        $savedFiles = [];

        foreach ($this->attachments as $locale => $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $originalFileName = $file->getClientOriginalName();
                $path = $file->store('mail_attachments');
                $fullPath = storage_path("app/private/{$path}");
                $savedFiles[$locale] = [
                    'local' => $fullPath,
                    'original' => $originalFileName,
                ];
            } else {
                Log::error('Invalid file detected:', ['file' => $file]);
            }
        }

        $memberCount = 0;
        $mailingListCount = 0;

        // --- Send to members ---
        foreach (Member::all() as $member) {
            if (! $member->email) {
                continue;
            }

            $url = $this->url ?? '';
            $label = $this->urlLabel[$member->locale] ?? null;

            $attachmentForLocale = ! empty($savedFiles[$member->locale])
                ? [$savedFiles[$member->locale]]
                : null;

            Mail::to($member->email)
                ->locale($member->locale)
                ->queue(new SendMemberMassMail(
                    $member->fullName(),
                    $this->subject[$member->locale],
                    $this->message[$member->locale],
                    $member->locale,
                    $url,
                    $label,
                    $attachmentForLocale,
                    $this->setPersonalGreeting,
                    $this->setAttachment,
                    $this->setLink
                ));

            $memberCount++;
        }

        // --- Send to mailing-list subscribers ---
        if ($this->include_mailing_list) {
            $memberEmails = Member::all()
                ->filter(fn ($member): bool => $member->email !== null)
                ->pluck('email')
                ->map(fn ($email) => strtolower($email))
                ->toArray();

            $mailingListSubscribers = MailingList::query()
                ->subscribed()
                ->where('update_on_notifications', true)
                ->whereNotNull('terms_accepted_at')
                ->where('terms_accepted', true)
                ->get();

            foreach ($mailingListSubscribers as $subscriber) {
                if (in_array(strtolower($subscriber->email), $memberEmails)) {
                    continue;
                }

                $locale = $subscriber->locale ?? 'de';
                $url = $this->url ?? '';
                $label = $this->urlLabel[$locale] ?? null;

                $attachmentForLocale = ! empty($savedFiles[$locale])
                    ? [$savedFiles[$locale]]
                    : null;

                Mail::to($subscriber->email)
                    ->locale($locale)
                    ->queue(new SendMemberMassMail(
                        $subscriber->email,
                        $this->subject[$locale],
                        $this->message[$locale],
                        $locale,
                        $url,
                        $label,
                        $attachmentForLocale,
                        $this->setPersonalGreeting,
                        $this->setAttachment,
                        $this->setLink
                    ));

                $mailingListCount++;
            }
        }

        $totalCount = $memberCount + $mailingListCount;

        // --- Persist sent mailing for documentation ---
        MailingHistory::create([
            'user_id' => Auth::id(),
            'subject' => $this->subject,
            'message' => $this->message,
            'url' => $this->url ?: null,
            'url_label' => $this->urlLabel ?? null,
            // Only store original filenames – actual files will be deleted shortly
            'attachments' => ! empty($savedFiles)
                ? collect($savedFiles)
                    ->map(fn ($f, $locale): array => ['locale' => $locale, 'original' => $f['original']])
                    ->values()
                    ->toArray()
                : null,
            'include_mailing_list' => $this->include_mailing_list,
            'set_link' => $this->setLink,
            'set_attachment' => $this->setAttachment,
            'set_personal_greeting' => $this->setPersonalGreeting,
            'recipient_count' => $totalCount,
            'member_count' => $memberCount,
            'mailing_list_count' => $mailingListCount,
        ]);

        Flux::toast('Die E-Mail wurde an '.$totalCount.' verschickt!', 'Erfolg', 6000, 'success');

        Flux::modal('confirm-sen-mass-mails')->close();

        DeleteEmailAttachments::dispatch($savedFiles)
            ->delay(now()->addMinutes(5));
    }

    public function sendTestMailToSelf(): void
    {
        $this->checkPrivilege(MailingList::class);
        $user = Auth::user();

        try {
            Mail::to($user->email)
                ->queue(new SendMemberMassMail(
                    (string) $user->name,
                    (string) $this->subject[$user->locale],
                    (string) $this->message[$user->locale],
                    $user->locale,
                    $this->url,
                    (string) $this->urlLabel[$user->locale],
                    null
                ));
            Flux::toast('Testmail sent');
        } catch (Exception $exception) {
            Flux::toast('Testmail not sent '.$exception->getMessage());
        }
    }

    protected function rules(): array
    {
        $rules = [
            'attachments.*' => 'file|max:20480',
        ];

        foreach (Locale::getNames() as $locale) {

            $rules = array_merge($rules, ["subject.{$locale}" => 'required'], ["message.{$locale}" => 'required']);

            if ($this->setLink) {
                $rules = array_merge($rules, ["urlLabel.{$locale}" => 'required']);
            }
        }

        if ($this->setLink) {
            $rules = array_merge($rules, ['url' => 'required|url']);
        } else {
            $rules = array_merge($rules, ['url' => 'nullable|url']);
        }

        return $rules;
    }

    public function addDummyData(): void
    {
        foreach (Locale::active()->pluck('name') as $locale) {
            $this->subject[$locale] = fake()->realText(50);
            $this->message[$locale] = fake()->realTextBetween(20);
            if ($this->setLink) {
                $this->urlLabel[$locale] = fake()->words(2, true);
            }
        }
        $this->url = 'commu-core.org';
    }

    public function mount(): void
    {
        $this->monthlySubscriptions = $this->subscriptionCurrentMonth();
        $this->yearlySubscriptions = $this->subscriptionCurrentYear();
        $this->totalSubscriptionsThisYear = $this->totalSubscriptionCurrentYear();
        $this->activeLocales = Locale::getNames();
    }

    public function render(): View
    {
        return view('livewire.app.tool.index.page')->title(__('mails.page_title'));
    }
}
