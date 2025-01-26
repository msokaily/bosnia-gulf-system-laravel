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
        Log::info('ChatGPT Response', ['response' => $chatResponse->content, 'contains_done' => str_contains($chatResponse->content, '[DONE]'), 'contains_issue' => str_contains($chatResponse->content, '[CLIENT_ISSUE]')]);
        if (str_contains($chatResponse->content, '[DONE]')) {
            $chatResponse = Chatgpt::sendMessage($request->input('phone'), null, null, true); // Summery The Order
            $whatsResponseEmp = WhatsApp::sendMessage('972595277518', "الرقم: ".$request->input('phone')."\n".$chatResponse->content); // Send To Employee
            $whatsResponse = WhatsApp::sendMessage($request->input('phone'), 'تم إستلام طلبك نجاح, سوف يتواصل مع حضرتكم موظف من طرفنا لتأكيد الحجز بشكل نهائي إن شاء الله.'); // To Client
            return $this->resJson(['chatResponse' => $chatResponse, 'whatsResponse' => $whatsResponse, 'whatsResponseEmp' => $whatsResponseEmp]);
        } else if (str_contains($chatResponse->content, '[CLIENT_ISSUE]')) {
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
        if ($request->input('hub_mode') == 'subscribe') {
            if ($request->input('hub_verify_token') == env('WHATSAPP_API_ACCESS')) {
                return $request->input('hub_challenge');
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthrozed'
                ], 401);
            }
        }
        
        $payload = file_get_contents('php://input');

        $decode = json_decode($payload,true);
        
        $message = $decode['entry'][0]['changes']['0']['value'];

        if (!isset($message['messages'])) {
            return;
        }

        $ownerno = $message['metadata']['display_phone_number'];
        $ownernoId = $message['metadata']['phone_number_id'];
        $username = 'UNKNOWN';
        if (isset($message['contacts'])) {
            $username = $message['contacts'][0]['profile']['name'];
        }
        $userno = $message['messages'][0]['from'];
        $usermessage = $message['messages'][0]['text']['body'];
        return $this->chatgpt_send_response($userno, $usermessage, $ownernoId);
    }

    private function chatgpt_send_response($userno, $usermessage, $ownernoId)
    {
        $chatResponse = Chatgpt::sendMessage($userno, $usermessage);
        Log::info('ChatGPT Response', ['response' => $chatResponse->content, 'contains_done' => str_contains($chatResponse->content, '[DONE]'), 'contains_issue' => str_contains($chatResponse->content, '[CLIENT_ISSUE]')]);
        if (str_contains($chatResponse->content, '[DONE]')) {
            $chatResponse = Chatgpt::sendMessage($userno, null, null, true); // Summery The Order
            $whatsResponseEmp = WhatsApp::sendMessage('972595277518', "الرقم: ".$userno."\n".$chatResponse->content, $ownernoId); // Send To Employee
            $whatsResponse = WhatsApp::sendMessage($userno, 'تم إستلام طلبك نجاح, سوف يتواصل مع حضرتكم موظف من طرفنا لتأكيد الحجز بشكل نهائي إن شاء الله.'); // To Client
            return $this->resJson(['chatResponse' => $chatResponse, 'whatsResponse' => $whatsResponse, 'whatsResponseEmp' => $whatsResponseEmp]);
        } else if (str_contains($chatResponse->content, '[CLIENT_ISSUE]')) {
            $chatResponse = Chatgpt::sendMessage($userno, null, null, true); // Summery The Order
            $whatsResponseEmp = WhatsApp::sendMessage('972595277518', "الرقم: ".$userno."\n".$chatResponse->content."\nهناك مشكلة مع العميل, يرجى حلها.", $ownernoId); // Send To Employee
            $whatsResponse = WhatsApp::sendMessage($userno, 'نعتذر عما حدث, سوف نقوم بحل المشكلة حالاً.', $ownernoId); // To Client
            return $this->resJson(['chatResponse' => $chatResponse, 'whatsResponse' => $whatsResponse, 'whatsResponseEmp' => $whatsResponseEmp]);
        }
        $whatsResponse = WhatsApp::sendMessage($userno, $chatResponse->content, $ownernoId);
        return $this->resJson(['chatResponse' => $chatResponse, 'whatsResponse' => $whatsResponse]);
    }
    
    public function search_accommodation(Request $request)
    {
        return BookingApi::search_hotel('malak-regency', $request->all());
    }
}
