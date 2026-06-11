<?php

declare(strict_types=1);

namespace App\Livewire\App;

use Laravel\Jetstream\Http\Livewire\ApiTokenManager as JetstreamApiTokenManager;

class ApiTokenManager extends JetstreamApiTokenManager
{
    protected function displayTokenValue($token): void
    {
        $this->displayingToken = true;
        $this->plainTextToken = $token->plainTextToken; // vollständiges Format: {id}|{token}
        $this->dispatch('showing-token-modal');
    }
}
