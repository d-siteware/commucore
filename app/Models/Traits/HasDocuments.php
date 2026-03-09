<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocuments
{
    /** @return MorphMany<Document, covariant static> */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }
}
