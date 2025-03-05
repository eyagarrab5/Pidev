<?php
namespace App\Service;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class SmsService
{
    private Client $client;
    private string $brandName;

    public function __construct(string $nexmoApiKey, string $nexmoApiSecret, string $brandName)
    {
        $basic = new Basic($nexmoApiKey, $nexmoApiSecret);
        $this->client = new Client($basic);
        $this->brandName = $brandName;
    }

    public function sendSms(string $toNumber, string $text): bool
    {
        $response = $this->client->sms()->send(new SMS($toNumber, $this->brandName, $text));

        $message = $response->current();
        
        if ($message->getStatus() == 0) {
            return true; // SMS envoyé avec succès
        } else {
            throw new \Exception("Erreur d'envoi du SMS: " . $message->getStatus());
        }
    }
}