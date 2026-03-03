<?php

declare(strict_types=1);

namespace App\Livewire\App\Global\Mailinglist;

use App\Models\MailingList;
use App\View\Components\GuestLayout;
use Flux\Flux;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class Show extends Component
{
    public $token;

    public $mailingList;

    public $update_on_events;

    public $update_on_articles;

    public $update_on_notifications;

    public function mount($token): void
    {
        $this->token = $token;

        try {
            $this->mailingList = MailingList::where('verification_token', $token)->firstOrFail();
            // Initialize preferences for editing
            $this->update_on_events = $this->mailingList->update_on_events;
            $this->update_on_articles = $this->mailingList->update_on_articles;
            $this->update_on_notifications = $this->mailingList->update_on_notifications;
            app()->setLocale($this->mailingList->locale);
        } catch (ModelNotFoundException $exception) {
            Log::alert('provided token not found '.$exception->getMessage());
            $this->redirect(route('home'));
        }
    }

    public function verify(): void
    {
        if (! $this->mailingList->verified_at) {
            $this->mailingList->verify();
            $this->mailingList->generateNewToken(); // For future updates
            Flux::toast(__('mails.mailing_list.show.confirmation_msg'), __('mails.toast.header.success'), 7000, 'success');
        }
    }

    public function updatePreferences(): void
    {
        $this->mailingList->update([
            'update_on_events' => $this->update_on_events,
            'update_on_articles' => $this->update_on_articles,
            'update_on_notifications' => $this->update_on_notifications,
            'verification_token' => Str::random(40), // Refresh token
        ]);

        Flux::toast(__('mails.mailing_list.show.update_msg'), __('mails.toast.header.success'), 7000, 'success');

    }

    #[Layout(GuestLayout::class)]
    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.global.mailinglist.show');
    }
}
