<?php

declare(strict_types=1);

namespace App\Actions\Post;

use App\Livewire\Forms\Blog\PostForm;
use App\Models\Blog\Post;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class UpdatePost extends Action
{
    public static function handle(PostForm $form): Post
    {

        return DB::transaction(function () use ($form) {
            Post::query()->findOrFail($form->id)->update([
                'title' => $form->title,
                'slug' => $form->slug,
                'body' => $form->body,
                'user_id' => $form->user_id,
                'status' => $form->status,
                'label' => $form->label,
                'post_type_id' => $form->post_type_id,
                'published_at' => $form->published_at,
                'event_id' => $form->event_id,
            ]);

            return Post::query()->findOrFail($form->id);
        });

    }
}
