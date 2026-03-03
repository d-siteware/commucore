<?php

declare(strict_types=1);

namespace App\Actions\Global;

use App\Livewire\Forms\Global\MailingListForm;
use App\Models\MailingList;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class CreateMailingListEntry extends Action
{
    public static function handle(MailingListForm $form): MailingList
    {
        return DB::transaction(function () use ($form) {
            return MailingList::create([
                'email' => $form->email,
                'terms_accepted_at' => $form->terms_accepted_at
                    ?? ($form->terms_accepted ? now() : null),
                'terms_accepted' => $form->terms_accepted,
                'update_on_events' => $form->update_on_events,
                'update_on_articles' => $form->update_on_articles,
                'update_on_notifications' => $form->update_on_notifications,
                'locale' => $form->locale,
            ]);

        });

    }
}
