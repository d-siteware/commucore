<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

readonly class DatevCheck
{
    public string $label;

    public DatevCheckType $type;

    public bool $passed;

    public string $message;

    public function __construct(string $label, DatevCheckType $type, bool $passed, string $message = '')
    {
        $this->label = $label;
        $this->type = $type;
        $this->passed = $passed;
        $this->message = $message;
    }
}
