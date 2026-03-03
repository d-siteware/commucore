<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Global;

use App\Actions\Global\CreateMailingListEntry;
use App\Actions\Global\UpdateMailingListEntry;
use App\Models\MailingList;
use Livewire\Form;

final class MailingListForm extends Form
{
    protected MailingList $mailingListEntry;

    public ?int $id;

    public ?string $email;

    public bool $terms_accepted;

    public ?\Carbon\Carbon $terms_accepted_at = null;

    public bool $update_on_events;

    public bool $update_on_articles;

    public bool $update_on_notifications;

    public ?\Carbon\Carbon $verified_at;

    public ?string $verification_token;

    public ?string $locale;

    public function set(int $listId): void
    {
        $this->mailingListEntry = MailingList::query()->find($listId);
        $this->email = $this->mailingListEntry->email;
        $this->terms_accepted = $this->mailingListEntry->terms_accepted;
        $this->terms_accepted_at = $this->mailingListEntry->terms_accepted_at;
        $this->update_on_events = $this->mailingListEntry->update_on_events;
        $this->update_on_articles = $this->mailingListEntry->update_on_articles;
        $this->update_on_notifications = $this->mailingListEntry->update_on_notifications;
        $this->verified_at = $this->mailingListEntry->verified_at;
        $this->verification_token = $this->mailingListEntry->verification_token;
        $this->locale = $this->mailingListEntry->locale;
    }

    public function create(): MailingList
    {
        return CreateMailingListEntry::handle($this);
    }

    public function update(): MailingList
    {
        return UpdateMailingListEntry::handle($this, $this->mailingListEntry);
    }

    protected function rules(): array
    {
        return [
            'email' => 'required|email|unique:mailing_lists,email',
            'terms_accepted' => 'accepted',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => __('mails.mailing_list.validation_error.email'),
            'terms_accepted.accepted' => __('mails.mailing_list.validation_error.terms_accepted'),
        ];
    }
}
