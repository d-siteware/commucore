<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class WhatsAppController extends Controller
{
    public function sendMessage(Request $request)
    {
        $accessToken = config('services.whatsapp.token');
        $hook_url = config('services.whatsapp.hook_url');

        $respose = Http::withToken($accessToken)
            ->post($hook_url, [
                'messaging_product' => 'whatsapp',
                'to' => $request->input('to'),
                'type' => 'text',
                'text' => ['body' => $request->input('message')],
            ]
            );

        return response()->json($respose->json());
    }

    public function verify(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {

        Log::info('incomming request', ['request' => $request]);
        if ($request->get('hub_mode') === 'subscribe' && $request->get('hub_verify_token') === config('services.whatsapp.hook_token')) {

            Log::info('request verified', [
                'hub_verify_token' => $request->get('hub_verify_token'),
                'vs_token' => config('services.whatsapp.hook_token'),
                'hub_challenge' => $request->get('hub_challenge'),
            ]);

            return response($request->get('hub_challenge'), 200);
        }

        return response('Token mismatch', 403);
    }

    public function getMessage(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
    {
        $data = $request->all();

        Log::info('recieved data from whatsapp webhook:', $data);

        $message = $data['entry'][0]['changes'][0]['value']['messages'][0] ?? null;

        if ($message) {
            $from = $message['from'];
            $body = $message['text']['body'] ?? 'empty body';
            Log::info('recieved message from whatsapp webhook:', [$from < 'from', 'body' => $body]);

        }

        return response('OK', 200);
    }
}
