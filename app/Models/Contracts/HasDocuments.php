<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @template TModel of Model
 */
interface HasDocuments
{
    /** @return MorphMany<Document, TModel> */
    public function documents(): MorphMany;
}
