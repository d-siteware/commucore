<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
final class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        // UUID hier NICHT cachen – jede Instanz braucht eine eigene UUID
        return [
            'documentable_type' => null, // wird per forModel() gesetzt
            'documentable_id' => null,
            'uploaded_by_user_id' => User::factory(),
            'uuid' => fn () => Str::uuid()->toString(),
            'original_name' => $this->faker->word().'.pdf',
            'disk' => 'local',
            'path' => fn (array $attrs) => "documents/test/1/{$attrs['uuid']}",
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(10_000, 5_000_000),
            'category' => null,
            'label' => null,
            'notes' => null,
            'last_accessed_at' => null,
            'last_accessed_by_user_id' => null,
        ];
    }

    public function pdf(): static
    {
        return $this->state([
            'mime_type' => 'application/pdf',
            'original_name' => $this->faker->word().'.pdf',
        ]);
    }

    public function image(): static
    {
        return $this->state([
            'mime_type' => 'image/jpeg',
            'original_name' => $this->faker->word().'.jpg',
        ]);
    }

    public function word(): static
    {
        return $this->state([
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'original_name' => $this->faker->word().'.docx',
        ]);
    }

    public function email(): static
    {
        return $this->state([
            'mime_type' => 'message/rfc822',
            'original_name' => $this->faker->word().'.eml',
        ]);
    }

    public function forModel(\Illuminate\Database\Eloquent\Model $model): static
    {
        $type = \Illuminate\Support\Str::snake(class_basename($model::class));
        $key = $model->getKey();

        return $this->state([
            'documentable_type' => $model::class,
            'documentable_id' => $key,
            // uuid kommt aus definition() als closure → bleibt eindeutig
            'path' => fn (array $attrs) => "documents/{$type}/{$key}/{$attrs['uuid']}",
        ]);
    }
}
