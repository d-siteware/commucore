<?php

declare(strict_types=1);

namespace App\Livewire\App\Tool\Index;

use App\Jobs\DeleteEmailAttachments;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\Sortable;
use App\Mail\SendMemberMassMail;
use App\Models\MailHistoryEntry;
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
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

final class Page extends Component
{
    use HasPrivileges;
    use Sortable;
    use WithFileUploads;
    use WithPagination;

    public array $subject;

    public array $message;

    public array $attachments;

    public bool $include_mailing_list = false;

    public bool $target_type;

    public array $monthlySubscriptions = [];

    public array $yearlySubscriptions = [];

    public int $totalSubscriptionsThisYear;

    public ?array $urlLabel;

    public string $url = '';

    public bool $setLink = true;

    public bool $setAttachment = true;

    public bool $setPersonalGreeting = true;

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
            ->map(fn ($item) => ['date' => $item->date, 'visitors' => $item->visitors])
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
            ->map(fn ($item) => ['month' => $item->month, 'visitors' => $item->visitors])
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

        MailHistoryEntry::create([
            'user_id' => Auth::user()->id,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);
        $savedFiles = [];

        if (count($this->attachments) > 0) {

            foreach ($this->attachments as $locale => $file) {
                if ($file instanceof TemporaryUploadedFile) {
                    $originalFileName = $file->getClientOriginalName();
                    $path = $file->store('mail_attachments'); // Save file
                    $fullPath = storage_path("app/private/{$path}"); // Get absolute path
                    $savedFiles[$locale] = [
                        'local' => $fullPath,
                        'original' => $originalFileName,
                    ]; // Store full path

                } else {
                    Log::error('Invalid file detected:', ['file' => $file]);
                }
            }

            $counter = 0;
            foreach (Member::all() as $member) {
                if ($member->email) {
                    $url = $this->url ?? '';
                    $label = $this->urlLabel[$member->locale] ?? null;
                    Mail::to($member->email)
                        ->locale($member->locale)
                        ->queue(new SendMemberMassMail(
                            $member->fullName(),
                            $this->subject[$member->locale],
                            $this->message[$member->locale],
                            $member->locale,
                            $url,
                            $label,
                            [$savedFiles[$member->locale]],
                            $this->setPersonalGreeting,
                            $this->setAttachment,
                            $this->setLink
                        ));
                    $counter++;
                }
            }
        } else {
            // no attachments existing
            $counter = 0;
            foreach (Member::all() as $member) {

                if ($member->email) {
                    $url = $this->url ?? '';
                    $label = $this->urlLabel[$member->locale] ?? null;
                    Mail::to($member->email)
                        ->locale($member->locale)
                        ->queue(new SendMemberMassMail(
                            $member->fullName(),
                            $this->subject[$member->locale],
                            $this->message[$member->locale],
                            $member->locale,
                            $url,
                            $label,
                            null,
                            $this->setPersonalGreeting,
                            $this->setAttachment,
                            $this->setLink
                        ));
                    $counter++;
                }
            }

        }

        if ($this->include_mailing_list) {
            // TODO make stuuff
        }

        Flux::toast('Die E-Mail wurde an '.$counter.' verschickt!', 'Erfolg', 6000, 'success');

        DeleteEmailAttachments::dispatch($savedFiles)
            ->delay(now()->addMinutes(5));
    }

    public function sendTestMailToSelf(): void
    {
        $this->checkPrivilege(MailingList::class);
        $user = Auth::user();

        //        if (!is_string($this->subject[$user->locale])) {
        //            throw new \Exception('Subject must be a string, but '.gettype($this->subject[$user->locale]).' given.');
        //        }

        try {
            Mail::to($user->email)
                ->queue(new SendMemberMassMail(
                    (string) $user->name,
                    (string) $this->subject[$user->locale], // Ensure it's a string
                    (string) $this->message[$user->locale], // Ensure it's a string
                    $user->locale,
                    $this->url,
                    (string) $this->urlLabel[$user->locale], // Ensure it's a string
                    null
                ));
            Flux::toast('Testmail sent');
        } catch (Exception $exception) {
            Flux::toast('Testmail not sent '.$exception->getMessage());
        }
    }

    protected function rules(): array
    {
        return [
            'attachments.*' => 'file|max:20480',  // 20MB max
            'subject.hu' => 'required',
            'subject.de' => 'required',
            'message.hu' => 'required',
            'message.de' => 'required',
            'url' => 'nullable',
            'urlLabel.de' => 'nullable',
            'urlLabel.hu' => 'nullable',
        ];
    }

    public function addDummyData(): void
    {
        $this->subject['hu'] = fake()->realText(50);
        $this->subject['de'] = fake()->realText(50);
        $this->message['hu'] = fake()->realTextBetween(20);
        $this->message['de'] = fake()->realTextBetween(20);
        $this->url = 'commu-core.org';
        $this->urlLabel['hu'] = 'Kattincs ide';
        $this->urlLabel['de'] = 'Click hier';
    }

    public function mount(): void
    {
        $this->monthlySubscriptions = $this->subscriptionCurrentMonth();
        $this->yearlySubscriptions = $this->subscriptionCurrentYear();
        $this->totalSubscriptionsThisYear = $this->totalSubscriptionCurrentYear();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.tool.index.page')->title('Mailing List');
    }
}
