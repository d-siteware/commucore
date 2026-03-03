<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\Blog\Post;
use App\Models\Blog\PostImage;
use App\Models\Blog\PostType;
use App\Models\Event\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        $postType = $this->ensureReviewPostType();
        $events = Event::all();

        if ($events->isEmpty()) {
            $this->command->warn('PostsSeeder: Keine Events gefunden – bitte zuerst den EventsSeeder ausführen.');

            return;
        }

        foreach ($events as $event) {
            $this->createReviewPost($event, $postType, $user);
        }
    }

    private function ensureReviewPostType(): PostType
    {
        return PostType::firstOrCreate(
            ['slug' => 'review'],
            [
                'name' => ['de' => 'Veranstaltungsbericht', 'hu' => 'Rendezvénybeszámoló', 'en' => 'Event Review'],
                'slug' => 'review',
                'description' => 'Rückblicke und Berichte zu stattgefundenen Vereinsveranstaltungen.',
                'color' => '#6366F1',
            ]
        );
    }

    private function createReviewPost(Event $event, PostType $postType, User $user): void
    {
        // Event-Typ aus dem verknüpften Datensatz auslesen (Fallback: 'general')
        $eventType = $event->type ?? 'general';

        $reviewText = DemoPostText::randomReviewForType($eventType);

        $title = $reviewText['title'];
        $slug = collect($title)->map(fn (string $t) => Str::slug($t).'-'.$event->id)->toArray();

        /** @var Post $post */
        $post = Post::create([
            'title' => $title,
            'slug' => $slug,
            'body' => $reviewText['body'],
            'user_id' => $user->id,
            'status' => 'published',
            'label' => $reviewText['label'],
            'published_at' => $event->start_date
                ? Carbon::parse($event->start_date)->addDays(2)
                : Carbon::now()->subDays(rand(3, 30)),
            'post_type_id' => $postType->id,
            'event_id' => $event->id,
        ]);

        PostImage::create([
            'post_id' => $post->id,
            'filename' => "demo/review-{$post->id}.jpg",
            'original_filename' => 'event-review.jpg',
            'caption' => [
                'de' => 'Eindrücke vom '.($event->title['de'] ?? 'Event'),
                'hu' => 'Benyomások a '.($event->title['hu'] ?? 'rendezvényről'),
                'en' => 'Impressions from '.($event->title['en'] ?? 'the event'),
            ],
            'author' => 'Redaktion',
        ]);
    }
}
