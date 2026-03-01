<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Event;

use App\Actions\Event\CreateEvent;
use App\Enums\EventStatus;
use App\Enums\Locale;
use App\Models\Accounting\Account;
use App\Models\Event\Event;
use App\Rules\UniqueJsonSlug;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class EventForm extends Form
{
    public Event $event;

    public string $locale;

    public array $locales;

    public $status = EventStatus::DRAFT->value;

    public $name;

    public $id;

    public $event_date;

    public $start_time;

    public $end_time;

    public $image;

    public $entry_fee;

    public $entry_fee_discounted;

    public $payment_link;

    public array $title;

    #[Validate]
    public array $slug;

    public array $excerpt;

    public array $description;

    public $venue_id;

    public $notification_sent_at;

    public function setEvent(Event $event): void
    {
        //        Log::debug('event object recieved in form: ', ['event' => $event->id]);
        $this->event = $event;
        $this->locale = session('locale') ?? app()->getLocale();
        $this->locales = \App\Models\Locale::getNames();
        $this->name = $this->event->name;
        $this->event_date = $this->event->event_date->format('Y-m-d');
        $this->id = $this->event->id;
        $this->start_time = $this->event->start_time->format('H:i');
        $this->end_time = $this->event->end_time->format('H:i');
        $this->status = $this->event->status;
        $this->title = $this->event->title;
        $this->excerpt = $this->event->excerpt;
        $this->slug = $this->event->slug;
        $this->payment_link = $this->event->payment_link;
        $this->description = $this->event->description;
        $this->entry_fee = Account::formatedAmount($this->event->entry_fee);
        $this->entry_fee_discounted = Account::formatedAmount($this->event->entry_fee_discounted);
        $this->venue_id = $this->event->venue_id;
        $this->notification_sent_at = $this->event->notification_sent_at;
    }

    public function create(): Event
    {
        $this->validate();
        $this->setEventTimes();

        return CreateEvent::handle($this);
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required',
            'venue_id' => 'nullable|exists:venues,id',
            'event_date' => ['required', 'date'], // 'required|date|after:today'
            'start_time' => 'required_with:event_date',
            'end_time' => 'required_with:event_date',
            'title.*' => [  // Using wildcard for each locale key
                'required',
                new UniqueJsonSlug('events', 'title', $this->id),
            ],
            'slug.*' => ['required', new UniqueJsonSlug('events', 'slug', $this->id)],
            'excerpt' => 'nullable',
            'description' => 'nullable',
            'image' => 'nullable',
            'payment_link' => 'nullable',
            'status' => ['nullable', Rule::enum(EventStatus::class)],
            'entry_fee' => 'nullable',
            'entry_fee_discounted' => 'nullable',
        ];

        if ($this->id === null) {
            Rule::date()->afterOrEqual(today());
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('event.validation.name.required'),
            'event_date.after' => __('event.validation.event_date.after'),
            'title.*.required' => __('event.validation.title.required'),
            'entry_fee.numeric' => __('event.validation.entry_fee.numeric'),
            'entry_fee_discounted.numeric' => __('event.validation.entry_fee_discounted.numeric'),
        ];
    }

    public function update(): void
    {
        $this->validate();
        $this->setEventTimes();
        $this->event->event_date = $this->event_date;
        $this->event->title = $this->title;
        $this->event->excerpt = $this->excerpt;
        $this->event->slug = $this->slug;
        $this->event->description = $this->description;
        $this->event->entry_fee = Account::makeCentInteger($this->entry_fee);
        $this->event->entry_fee_discounted = Account::makeCentInteger($this->entry_fee_discounted);
        $this->event->venue_id = $this->venue_id;
        $this->event->payment_link = $this->payment_link;
        $this->event->status = $this->status;
        $this->event->name = $this->name;
        $this->event->notification_sent_at = $this->notification_sent_at;

        if ($this->event->save()) {
            Flux::toast(
                text: __('event.update.success.content'),
                heading: __('event.update.success.title'),
                variant: 'success',
            );
        }
    }

    public function storeImage($file): bool
    {
        $this->event->image = $file;

        return $this->event->save();
    }

    public function deleteImage(): bool
    {
        try {
            $del = Storage::disk('public')
                ->delete('/image/images/'.$this->event->image);
            if ($del) {
                $this->event->image = null;

                return $this->event->save();
            }
        } catch (FileNotFoundException $e) {
            Flux::toast(
                text: 'Die Datei konnte nicht gelköscht werden => '.$e->getMessage(),
                heading: 'Fehler',
                variant: 'danger',
            );
        }

        return false;
    }

    public function makeWebText(): void
    {
        foreach (Locale::cases() as $locale) {
            if (isset($this->title[$locale->value])) {
                $this->slug[$locale->value] = date('Y').'-'.Str::slug($this->title[$locale->value]);
            }

            if (isset($this->description[$locale->value])) {
                $this->excerpt[$locale->value] = Str::of($this->description[$locale->value])
                    ->stripTags(['<p>', '<strong>', '<br>'])->toString();
                $this->excerpt[$locale->value] = str_replace('.', '. ', $this->excerpt[$locale->value]);
                $this->excerpt[$locale->value] = Str::of($this->excerpt[$locale->value])
                    ->limit(200, ' ..', true);
            }
        }
    }

    public function demoData(): void
    {
        $this->event_date = fake()->dateTimeBetween('today', '+1 year');
        $this->name = fake()->realText(50);
        $this->title['de'] = fake()->realText(50);
        $this->description['de'] = fake()->randomHtml(12, 8);

        $this->title['hu'] = fake()->realText(50);
        $this->description['hu'] = fake()->randomHtml(12, 8);
    }

    private function setEventTimes(): void
    {
        $date = $this->event_date;
        $start = $this->start_time; // e.g. 14:00
        $end = $this->end_time;     // e.g. 16:00
        $this->event->start_time = Carbon::createFromTimeString("{$date} {$start}:00"); // => 2025-08-10 14:00:00
        $this->event->end_time = Carbon::createFromTimeString("{$date} {$end}:00");
    }
}
