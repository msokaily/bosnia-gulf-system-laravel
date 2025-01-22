<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\BookingApi;
use App\Services\Chatgpt;
use App\Services\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function chatgpt_send_test(Request $request)
    {
        $chatResponse = Chatgpt::sendMessage($request->input('phone'), $request->input('message'));
        if ($chatResponse->content == '[DONE]') {
            $chatResponse = Chatgpt::sendMessage($request->input('phone'), null, null, true); // Summery The Order
            $whatsResponseEmp = WhatsApp::sendMessage('972595277518', "الرقم: ".$request->input('phone')."\n".$chatResponse->content); // Send To Employee
            $whatsResponse = WhatsApp::sendMessage($request->input('phone'), 'تم إستلام طلبك نجاح, سوف يتواصل مع حضرتكم موظف من طرفنا لتأكيد الحجز بشكل نهائي إن شاء الله.'); // To Client
            return $this->resJson(['chatResponse' => $chatResponse, 'whatsResponse' => $whatsResponse, 'whatsResponseEmp' => $whatsResponseEmp]);
        } else if ($chatResponse->content == '[CLIENT_ISSUE]') {
            $chatResponse = Chatgpt::sendMessage($request->input('phone'), null, null, true); // Summery The Order
            $whatsResponseEmp = WhatsApp::sendMessage('972595277518', "الرقم: ".$request->input('phone')."\n".$chatResponse->content."\nهناك مشكلة مع العميل, يرجى حلها."); // Send To Employee
            $whatsResponse = WhatsApp::sendMessage($request->input('phone'), 'نعتذر عما حدث, سوف نقوم بحل المشكلة حالاً.'); // To Client
            return $this->resJson(['chatResponse' => $chatResponse, 'whatsResponse' => $whatsResponse, 'whatsResponseEmp' => $whatsResponseEmp]);
        }
        $whatsResponse = WhatsApp::sendMessage($request->input('phone'), $chatResponse->content);
        return $this->resJson(['chatResponse' => $chatResponse, 'whatsResponse' => $whatsResponse]);
    }

    public function whatsapp_send_test(Request $request)
    {
        $response = WhatsApp::sendMessage('38761091424', 'مرحبا محمد كيف الحال!');
        return $this->resJson(['response' => $response]);
    }

    public function whatsapp_callback(Request $request)
    {
        Log::error('WhatsApp Received Message', ['data' => $request->all()]);
        if ($request->input('hub_mode') == 'subscribe') {
            return $request->input('hub_challenge');
        }
        
        $payload = file_get_contents('php://input');

        $decode = json_decode($payload,true);
        
        $ownerno = $decode['entry'][0]['changes']['0']['value']['metadata']['display_phone_number'];
        $ownernoId = $decode['entry'][0]['changes']['0']['value']['metadata']['phone_number_id'];
        // $username = $decode['entry'][0]['changes']['0']['value']['contacts'][0]['profile']['name'];
        $userno = $decode['entry'][0]['changes']['0']['value']['messages'][0]['from'];
        $usermessage = $decode['entry'][0]['changes']['0']['value']['messages'][0]['text']['body'];
        $chatResponse = Chatgpt::sendMessage($userno, $usermessage, $ownernoId);
        $whatsResponse = WhatsApp::sendMessage($userno, $chatResponse->content, $ownernoId);
        return $this->resJson(compact('chatResponse', 'whatsResponse'));
    }
    
    public function search_accommodation(Request $request)
    {
        return BookingApi::search_hotel('malak-regency', $request->all());
    }
}
