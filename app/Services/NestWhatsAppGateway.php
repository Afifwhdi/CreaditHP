<?php

namespace App\Services;

use App\Contracts\WhatsAppGateway;
use Illuminate\Support\Facades\Http;

class NestWhatsAppGateway implements WhatsAppGateway
{
    public function __construct(
        private readonly string $base,
        private readonly string $token
    ) {}

    public function send(string $to, string $message): void
    {
        Http::asJson()->withToken($this->token)
            ->post(rtrim($this->base, '/') . '/whatsapp/send', [
                'to'      => $to,
                'message' => $message,
            ])->throw();
    }
}
