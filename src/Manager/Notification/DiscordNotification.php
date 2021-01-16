<?php

namespace App\Manager\Notification;

class DiscordNotification
{

    private string $webhookUrl;

    private string $message;

    /**
     * DiscordNotification constructor.
     */
    public function __construct()
    {
        $this->webhookUrl = $_ENV['DISCORD_WEBHOOK'];
    }

    public function setMessage($message): DiscordNotification
    {
        $this->message = $message;

        return $this;
    }

    public function send(): void
    {
        $webhookurl = $this->webhookUrl;

        $json_data = json_encode([
            "content" => $this->message,

            "username" => "Quizzer",

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


        $ch = curl_init( $webhookurl );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        curl_exec( $ch );
        curl_close( $ch );
    }
}