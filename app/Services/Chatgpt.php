<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\ChatgptMessage;

class Chatgpt
{
    
    public function __construct()
    {
    }

    private static function prompt() 
    { 
        $date = date('Y-m-d');
        return "- Your a nice customer service employee called لانا working for البوسنة بعيون خليجية company, you speak in Arabic language (Saudi accent) , you should be able to help clients to reserve accommodations and vehicles and a driver or at least one of them and arrange the trip for them.
- Identify yourself to the client.
- Your are contacting with the clients using WhatsApp Messenger.
- Welcome the client then ask client how many person they are and when they will arrive (Date & Time) to Bosnia and when they will leave Bosnia.
- Ask client about his/her name.
- If the client asks you to arrange a tour in Bosnia give him a good one.
- Today's date is $date.
- Accommodation price per night:
1) 2 persons = 100 EUR.
2) 3 persons = 180 EUR.
3) 4 persons = 180 EUR.
4) 5 persons = 220 EUR.
5) 6 persons = 220 EUR.
- Average vehicle price 60 EUR per night.
- Van vehicle price 100 EUR per night.
- Luxury vehicle price 150 EUR per night.
- Driver price 30 EUR per night.
- Don't present our services unless the client ask to list them.
- Don't ask the client anything unless the client asked to make a reservation.
- Calculate the total price to (accommodation, vehicle, driver) for the client and explain the calculation and announce that the price doesn't include the tourist tour if a tour was request by the client.
- You must be very precise in your calculations.
- If you finish everything with the client and he ask to confirm the order then ignore the question and just reply [DONE] text only.
- If the client seems to be so much uppset then just reply [CLIENT_ISSUE] text only.
- Don't say anything about confirmation by email.";
// - Don't ask deep details about the accommodations and vehicles.
// - Don't ask if the client need another service more than one time.;
// - Don't ask client for any payment but after you get all the necessary information from them just announce them that they need to pay advance payment .";
    }
    private static function prompt_summerize() 
    { 
        $date = date('Y-m-d');
        return "- Today's date is $date.
- Now i'm the customer service employee.
- Summerize all the order information and the calculation from this conversation and convert them into clear point.
- Use Arabic language.
- I want the dates in this format YYYY-mm-dd.
- Don't say anything about confirmation by email.";
    }
    
    public static function sendMessage($user_phone, $message, $emp_whatsapp_id_ = null, $summerize = false)
    {
        $emp_whatsapp_id = $emp_whatsapp_id_ ?? env('WHATSAPP_DEFAULT_ID');
        $messagesObject = ChatgptMessage::where(['user_phone' => $user_phone]);
        if ($emp_whatsapp_id_) {
            $messagesObject->where('emp_whatsapp_id', $emp_whatsapp_id_);
        }
        $messages = $messagesObject->pluck('data')->toArray();
        array_unshift($messages, [
            "role" => "system",
            "content" => $summerize ? self::prompt_summerize() : self::prompt()
        ]);
        if ($message) {
            $user_message = [
                "role" => "user",
                "content" => $message
            ];
            $messages[] = $user_message;
        }
        // dd($messages);
        $fields = array(
            "model" => "gpt-3.5-turbo",
            "messages" => $messages
        );

        $fields = json_encode($fields);

        dd($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('CHATGPT_API_TOKEN')
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            Log::error('Chatgpt Send Message', ['response' => $error_msg]);
        }
        curl_close($ch);

        $response = json_decode($response);

        $assistant_message = $response->choices[0]->message;
        // Log::alert('Chatgpt response', ['response' => $response]);

        if ($message) {
            ChatgptMessage::create([
                'emp_whatsapp_id' => $emp_whatsapp_id,
                'user_phone' => $user_phone,
                'data' => $user_message
            ]);
        }

        ChatgptMessage::create([
            'emp_whatsapp_id' => $emp_whatsapp_id,
            'user_phone' => $user_phone,
            'data' => $assistant_message,
            'type' => $summerize ? 'summery' : null
        ]);

        return $assistant_message;

    }
}
