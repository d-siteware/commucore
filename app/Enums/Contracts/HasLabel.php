<?php

declare(strict_types=1);

namespace App\Enums\Contracts;

interface HasLabel
{
    public function label(): string;
}
