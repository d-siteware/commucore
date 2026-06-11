<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Public\V1;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EventDetailResource;
use App\Http\Resources\Api\V1\EventListResource;
use App\Http\Resources\v1\Event\EventResource;
use App\Models\Event\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

final class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $locale = $request->string('locale', 'de')->toString();
        $upcoming = $request->boolean('upcoming', false);
        $limit = min((int) $request->integer('limit', 10), 50);

        $query = Event::query()
            ->where('status', EventStatus::PUBLISHED)
            ->orderBy('event_date');

        if ($upcoming) {
            $query->where('event_date', '>=', now()->toDateString());
        }

        $events = $query->paginate($limit);

        return EventListResource::collection($events)
            ->additional(['locale' => $locale]);
    }

    public function show(Request $request, Event $event): EventDetailResource
    {
        $locale = $request->string('locale', 'de')->toString();

        abort_if($event->status !== EventStatus::PUBLISHED, 404);

        $event->load(['venue', 'timelines']);

        return (new EventDetailResource($event))
            ->additional(['locale' => $locale]);
    }

    public function apiShow(string $slug): JsonResponse
    {
        $event = Event::findEventBySlug($slug, false) ?? abort(404);

        return response()->json([
            'data' => new EventResource($event),
            'meta' => [
                'locale' => App::getLocale(),
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
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
}
