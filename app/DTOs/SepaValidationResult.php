<?php

declare(strict_types=1);

namespace App\DTOs;

use LibXMLError;

final readonly class SepaValidationResult
{
    public function __construct(
        public bool $valid,
        public array $errors = [],
    ) {}

    public function summary(): string
    {
        if ($this->valid) {
            return __('sepa.validation.passed');
        }

        $messages = array_map(
            fn (LibXMLError $e) => __('sepa.validation.error_line', ['line' => $e->line, 'message' => trim($e->message)]),
            $this->errors,
        );

        return __('sepa.validation.failed', ['count' => count($this->errors)])."\n".implode("\n", $messages);
    }

    public function toFlash(): string
    {
        if ($this->valid) {
            return __('sepa.validation.passed');
        }

        $messages = array_map(
            fn (LibXMLError $e) => __('sepa.validation.error_line', ['line' => $e->line, 'message' => trim($e->message)]),
            $this->errors,
        );

        return __('sepa.validation.failed', ['count' => count($this->errors)]).' '.implode(' | ', $messages);
    }
}
