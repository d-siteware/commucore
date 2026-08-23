<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Contracts\DeletesUsers;

final class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        Log::warning('Self-deletion durchgeführt', [
            'scope' => 'instance',
            'user_id' => $user->id,
            'email' => $user->email,
            'subdomain' => $this->subdomain(),
            'is_last_admin' => $user->is_admin
                && User::where('is_admin', true)->whereKeyNot($user->id)->doesntExist(),
        ]);

        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }

    private function subdomain(): ?string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return $host !== null ? explode('.', $host)[0] : null;
    }
}
