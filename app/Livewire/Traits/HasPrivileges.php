<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait HasPrivileges
{
    use AuthorizesRequests;

    protected function checkPrivilege(string|Model $resource, string $action = 'create'): bool
    {
        try {
            $this->authorize($action, $resource);

            return true;
        } catch (AuthorizationException $e) {
            Flux::toast(
                text: 'You have no persmission to access '.(is_string($resource) ? $resource : get_class($resource)).'. => '.$e->getMessage(),
                heading: 'Forbidden',
                variant: 'danger',
            );

            return false; // Prevent further execution
        }
    }
}
