<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait HandlesErrors
{
    private function userErrorMessage(\Throwable $e): string
    {
        return match (true) {
            $e instanceof ValidationException => $e->getMessage(),
            $e instanceof ModelNotFoundException => __('common.model_not_found'),
            $e instanceof AuthorizationException => __('common.no_permission'),
            $e instanceof QueryException => __('common.database_error'),
            default => $e->getMessage() ?: __('common.error_occurred'),
        };
    }

    private function logError(string $context, \Throwable $e): void
    {
        Log::error($context, [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * @throws ValidationException Validierungsfehler werden re-thrown, damit
     *                             Livewire den Error-Bag füllt und die Felder
     *                             Inline-Fehler anzeigen (kein Toast).
     */
    private function handleError(string $context, \Throwable $e): void
    {
        if ($e instanceof ValidationException) {
            throw $e;
        }

        $this->logError($context, $e);
        Flux::toast(
            text: $this->userErrorMessage($e),
            heading: __('common.error'),
            variant: 'danger',
        );
    }
}
