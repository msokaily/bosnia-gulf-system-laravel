<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsApp
{
    public function __construct()
    {
    }
    public static function sendMessage($toWhatsNumber, $message, $fromWhatsNumberId_ = null)
    {
        $fromWhatsNumberId = $fromWhatsNumberId_ ?? env('WHATSAPP_DEFAULT_ID');
        $fields = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $toWhatsNumber,
            "type" => "text",
            "text" => [
                "body" => $message
            ]
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v20.0/$fromWhatsNumberId/messages");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('WHATSAPP_API_TOKEN')
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            Log::error('WhatsApp Send Message', ['response' => $error_msg, 'to' => $toWhatsNumber, 'from' => $fromWhatsNumberId]);
        }
        curl_close($ch);

        return json_decode($response);

    }
}
