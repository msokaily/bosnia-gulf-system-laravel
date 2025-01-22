<?php

namespace App\Services;

use App\Models\ActivitiesLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Notifications
{
    public function __construct()
    {
    }
    public static function sendOrderNotif($id, $type = 'new', $toRole = 'admin')
    {
        $order = Order::find($id);
        if (!$order) return;
        $lastUpdate = ActivitiesLog::where(['order_id' => $order->id])->orderBy('created_at', 'DESC')->first();

        $heading = array(
            "en" => 'New Reservation',
        );
        $content = array(
            "en" => 'Reservation to ' . $order->name . ' (#' . $order->id . ') has been added by ' . ($lastUpdate ? $lastUpdate->user->name : ''),
        );
        if ($type == 'update') {
            $heading = array(
                "en" => 'Update Reservation',
            );
            $content = array(
                "en" => 'Reservation to ' . $order->name . ' (#' . $order->id . ') has been modified by ' . ($lastUpdate ? $lastUpdate->user->name : ''),
            );
        }

        $tokens = [];
        $toUsers = User::where('role', $toRole)->get();
        foreach ($toUsers as $key => $value) {
            if ($value->push_token && is_array($value->push_token)) {
                $tokens = array_merge($tokens, $value->push_token);
            }
        }

        $fields = array(
            'app_id' => env('OneSignalAppId'),
            'data' => array("type" => "order", "order_id" => $id),
            "url" => env('APP_URL_FRONT').'/orders/'.$id,
            'include_subscription_ids' => $tokens,
            'contents' => $content,
            'headings' => $heading,
        );


        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('OneSignalAppRestAPI')
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            Log::error('Send Notification', ['response' => $error_msg, 'tokens' => $tokens]);
        }
        curl_close($ch);

    }
}
