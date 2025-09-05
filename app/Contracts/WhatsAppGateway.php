<?php

namespace App\Contracts;

interface WhatsAppGateway
{
    public function send(string $to, string $message): void;
}
