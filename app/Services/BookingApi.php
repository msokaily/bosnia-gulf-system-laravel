<?php

namespace App\Services;

use DOMDocument;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;
use HeadlessChromium\BrowserFactory;

class BookingApi
{

    private static $hotels = [
        "malak-regency" => "https://www.booking.com/hotel/ba/malak-regency.ar.html"
    ];
    
    public function __construct()
    {
    }

    public static function search_hotel($hotel_name, $options = [])
    {
        $hotel_link = self::$hotels[$hotel_name];

        $fields = [
            'checkin' => '2024-07-03',
            'checkout' => '2024-07-09',
            'group_adults' => '2',
            'group_children' => '1',
            'age' => '4',
            'no_rooms' => '1',
            'req_adults' => '2',
            'req_age' => '4',
            'req_children' => '1',
            'selected_currency' => 'EUR',
        ];

        $fields = array_merge($fields, $options);

        $fields = http_build_query($fields);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "$hotel_link?$fields");
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: text/html'
        // ));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // curl_setopt($ch, CURLOPT_POST, FALSE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        // $response = curl_exec($ch);

        // if (curl_errno($ch)) {
        //     $error_msg = curl_error($ch);
        //     // Log::error('Chatgpt Send Message', ['response' => $error_msg]);
        // }
        // curl_close($ch);

        $response = self::parseHtml("$hotel_link?$fields");

        return $response;

    }

    public static function parseHtml($url)
    {
        // $dom = new Dom;
        // $dom->loadFromUrl($url);
        // // $rooms = $dom->find('.hp-group_recommendation__table');
        // // $rooms = $dom->find('#hotelTmpl .hotelchars')[0];
        // return $dom->outerHtml;

    }
}
