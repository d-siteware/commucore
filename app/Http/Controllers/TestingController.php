<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event\Event;
use App\Models\Membership\Member;

final class TestingController extends Controller
{
    /**
     * Temporary route for testing email template rendering.
     * TODO: Remove after testing is complete.
     */
    public function mailTest(): \Illuminate\View\View
    {
        app()->setLocale('de');

        return view('emails.new_event_notification',[
            'recipient' => [
                'locale' => 'de',
                'id' => 1,
                'type'=> 'member'
            ],
            'notifiable' => Event::find(1),
            'notificationType' => 'events'
        ]);

//        return view('emails.invitation', ['member' => Member::find(1)]);
    }
}
