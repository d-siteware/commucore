<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait HasPrivileges
{
    use AuthorizesRequests;

    protected function checkPrivilege(string|Model $resource, string $action = 'create'): bool
    {
        // Throw on denial so callers cannot accidentally continue after an ignored false return.
        $this->authorize($action, $resource);

        return true;
    }
}
