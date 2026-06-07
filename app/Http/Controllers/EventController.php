<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App;
use App\Enums\EventStatus;
use App\Enums\Locale;
use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Services\IcsGeneratorService;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class EventController extends Controller
{
    public function generateIcs(string $slug, IcsGeneratorService $service): Response
    {
        return $service->generate($slug);
    }

    public function index(): View
    {
        $recentEvents = Event::query()
            ->with('venue:id,name,address,city')
            ->whereBeforeToday('event_date')
            ->where('status', EventStatus::PUBLISHED->value)
            ->orderByDesc('event_date')
            ->take(5)->get();

        $todayEvents = Event::query()
            ->with('venue:id,name,address,city')
            ->whereToday('event_date')
            ->where('status', EventStatus::PUBLISHED->value)->take(5)->get();

        $upcomingEvents = Event::query()
            ->with('venue:id,name,address,city')
            ->whereAfterToday('event_date')
            ->where('status', EventStatus::PUBLISHED->value)
            ->orderBy('event_date')
            ->take(5)->get();

        return view('events.index', [
            'todayEvents' => $todayEvents,
            'upcomingEvents' => $upcomingEvents,
            'recentEvents' => $recentEvents,
            'locale' => App::getLocale(),
        ]);
    }

    public function show(string $slug): View
    {
        $event = Event::findEventBySlug($slug);

        if (! $event instanceof Event) {
            abort(404);
        }

        $locale = null;
        foreach (App\Models\Locale::getNames() as $availableLocale) {
            if (($event->slug[$availableLocale] ?? null) === $slug) {
                $locale = $availableLocale;
                break;
            }
        }

        // Fallback auf default locale wenn nicht gefunden
        if (! $locale) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        $related_posts = $event->relatedPosts();
        $posts_count = $related_posts->count();

        return view('events.show', [
            'event' => $event,
            'locale' => $locale,
            'relatedPosts' => $related_posts,
            'relatedPostsCount' => $posts_count,
        ]);
    }

    /**
     * @return View
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function confirmSubscription(int $eventSubscriptionId, string $token)
    {
        $storedToken = cache()->get("event_subscription_{$eventSubscriptionId}_token");

        //        Log::debug('compare tokens', ['token' => $token, 'eventSubscription' => $eventSubscriptionId, 'storedToken' => $storedToken]);

        if ($storedToken && $storedToken === $token) {

            $eventSubscription = EventSubscription::query()
                ->findOrFail($eventSubscriptionId);
            $eventSubscription->update(['confirmed_at' => now()]);
            session()->flash('status', 'Deine Anmeldung wurde bestätigt! 🎉');
            cache()->forget("event_subscription_{$eventSubscription->id}_token");

            return $this->show($eventSubscription->event->slug[app()->getLocale()]);
        }

        abort(403);
    }
}
