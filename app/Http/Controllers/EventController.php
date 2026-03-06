<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App;
use App\Enums\EventStatus;
use App\Enums\Locale;
use App\Http\Resources\v1\Event\EventResource;
use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Services\IcsGeneratorService;
use Illuminate\Http\JsonResponse;
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
        $event = $this->findEventBySlug($slug);

        if (! $event instanceof \App\Models\Event\Event) {
            abort(404);
        }

        $locale = null;
        foreach (\App\Models\Locale::getNames() as $availableLocale) {
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

    public function apiShow(string $slug): JsonResponse
    {
        $event = $this->findEventBySlug($slug, false) ?? abort(404);

        return response()->json([
            'data' => new EventResource($event),
            'meta' => [
                'locale' => App::getLocale(),
                'timestamp' => now()->toIso8601String(),
            ],
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

    public function apiAll(): JsonResponse
    {
        $events = Event::query()
            ->with('venue')
            ->where('status', EventStatus::PUBLISHED->value)
            ->get();

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'count' => $events->count(),
                'locale' => App::getLocale(),
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    public function apiIndex(): JsonResponse
    {
        $events = Event::query()
            ->with('venue')
            ->where('status', EventStatus::PUBLISHED->value)
            ->where('event_date', '>', now()->toIso8601String())
            ->get();

        return response()->json([
            'data' => EventResource::collection($events),
            'meta' => [
                'count' => $events->count(),
                'locale' => App::getLocale(),
                'timestamp' => now()->toIso8601String(),
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function rssFeed(): Response
    {
        $events = Event::query()
            ->with('venue')
            ->where('status', EventStatus::PUBLISHED->value)
            ->where('event_date', '>', now()->toIso8601String())
            ->orderBy('event_date', 'desc')
            ->get();

        $locale = app()->getLocale();

        return response()
            ->view('feed.events', [
                'events' => $events,
                'locale' => $locale,
                'lastBuildDate' => $events->isNotEmpty() ? $events->first()->event_date->toRssString() : now()->toRssString(),
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    private function findEventBySlug(string $slug, bool $withRelations = true): ?Event
    {
        $query = Event::query()
            ->where('status', EventStatus::PUBLISHED->value)
            ->where(function ($query) use ($slug): void {
                // Dynamisch über alle verfügbaren Locales iterieren
                foreach (\App\Models\Locale::getNames() as $locale) {
                    $query->orWhereJsonContains("slug->{$locale}", $slug);
                }
            });

        if ($withRelations) {
            $query->with(['venue', 'posts', 'timelines']);
        }

        return $query->first();
    }
}
