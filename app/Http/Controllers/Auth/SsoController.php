<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SsoController extends Controller
{
    /**
     * SSO-Token empfangen, validieren und User einloggen.
     * Route: GET /auth/sso?token={token}
     *
     * Token-Format: {payload_base64url}.{hmac}
     * Payload:      base64url({email}|{subdomain}|{expires_unix}|{redirect})
     */
    public function login(Request $request)
    {
        $token = $request->query('token', '');

        // Format prüfen
        $dotPos = strrpos($token, '.');
        if (!$token || $dotPos === false) {
            return $this->fail('Ungültiges Token-Format.');
        }

        $payload = substr($token, 0, $dotPos);
        $hmac    = substr($token, $dotPos + 1);

        // Signatur prüfen
        $secret   = config('sso.secret');
        $expected = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expected, $hmac)) {

            Log::debug('Token-Signatur ungültig',[
                'expected' => $expected,
                'hmac' => $hmac,
                'payload' => $payload,
                'secret' => $secret,
            ]);

            return $this->fail('Token-Signatur ungültig.');
        }

        // Payload dekodieren
        $data = $this->decodePayload($payload);
        if (!$data) {
            return $this->fail('Token-Payload konnte nicht gelesen werden.');
        }

        // Abgelaufen?
        if (now()->timestamp > $data['expires']) {
            return $this->fail('Token ist abgelaufen. Bitte erneut anmelden.');
        }

        // Richtige Instanz?
        $currentSubdomain = explode('.', $request->getHost())[0];
        if ($data['subdomain'] !== $currentSubdomain) {
            return $this->fail('Token ist für eine andere Instanz ausgestellt.');
        }

        // User finden
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return $this->fail('Benutzer nicht gefunden.');
        }

        // Einloggen
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect($data['redirect']);
    }

    private function decodePayload(string $payload): ?array
    {
        try {
            $decoded = base64_decode(strtr($payload, '-_', '+/'));
            $parts   = explode('|', $decoded, 4);

            if (count($parts) < 3) return null;

            return [
                'email'     => $parts[0],
                'subdomain' => $parts[1],
                'expires'   => (int) $parts[2],
                'redirect'  => $parts[3] ?? '/dashboard',
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function fail(string $message)
    {
        Log::error('SSO-Login fehlgeschlagen: '.$message);
        return redirect('/login')->withErrors(['sso' => $message]);
    }
}