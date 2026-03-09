<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @template TModel of Model
 *
 * @covariant TModel
 */
interface HasDocuments
{
    /** @return MorphMany<Document, covariant TModel> */
    public function documents(): MorphMany;
}
