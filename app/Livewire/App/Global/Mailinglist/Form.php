<?php

declare(strict_types=1);

namespace App\Livewire\App\Global\Mailinglist;

use App\Livewire\Forms\Global\MailingListForm;
use App\Mail\ConfirmMailingListSubscription;
use App\Models\MailingList;
use Flux\Flux;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

final class Form extends Component
{
    public MailingListForm $form;

    public function mount(): void
    {
        $this->form->update_on_events = false;
        $this->form->update_on_articles = false;
        $this->form->update_on_notifications = false;
        $this->form->locale = app()->getLocale();
    }

    public function addMailingListEntry(): void
    {

        $this->form->validate();

        /** @var MailingList $subscriber */
        $subscriber = $this->form->create();

        Mail::to($subscriber->email)->locale($subscriber->locale)->send(new ConfirmMailingListSubscription($subscriber));

        Flux::toast(
            text: __('mails.mailing_list.subscription_success'),
            variant: 'success'
        );

        $this->reset();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.app.global.mailinglist.form');
    }
}
