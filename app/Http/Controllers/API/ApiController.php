<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\WhatsappMessage;
use App\Services\BookingApi;
use App\Services\Chatgpt;
use App\Services\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function whatsapp_callback1(Request $request)
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

        Log::info('Whatsapp Client Message', ['response' => $message['messages'][0]]);
        exit;

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

        $decode = json_decode($payload, true);

        if (!isset($decode['entry'][0]['changes']['0']['value'])) {
            return;
        }

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
        // Log::info('NewMessage', ['msgs' => $message['messages']]);

        // Check if a message is received
        if (!empty($message['messages'])) {
            foreach ($message['messages'] as $msg) {
                $files = [];
                $usermessage = '';
                if (isset($message['messages'][0]['text'])) {
                    $usermessage = $message['messages'][0]['text']['body'];
                }
                $sender = $msg['from']; // Sender's phone number
                if (isset($msg['type']) && in_array($msg['type'], ['image', 'video', 'audio', 'document'])) {
                    $mediaId = $msg[$msg['type']]['id']; // Get media ID
                    if(isset($msg[$msg['type']]['caption'])) {
                        $usermessage = $msg[$msg['type']]['caption'];
                    }
                    // Download media file
                    $media_db_id = $this->downloadMedia($mediaId);
                    if ($media_db_id) {
                        Log::info("Media received from $sender: $media_db_id");
                    }
                    $files[] = $media_db_id;
                }
                $this->chatgpt_send_response($sender, $usermessage, $ownernoId);
                $newMessage = WhatsappMessage::create([
                    'emp_whatsapp_id' => env('WHATSAPP_DEFAULT_ID'),
                    'user_phone' => $sender,
                    'sender' => $sender,
                    'message_id' => $message['messages'][0]['id'],
                    'data' => [
                        'message' => $usermessage,
                        'files' => $files
                    ]
                ]);
                // SendNotificationListener::sendNotif(SendNotificationListener::users_tokens([1]), $newMessage->data->message, __('common.new_message') . ' ' . $username, route('admin.home.edit', $sender), ['type' => 'chat', 'item' => $newMessage->toArray()]);
            }
        }
        return true;

    }

    function downloadMedia($mediaId)
    {
        $whatsappToken = env('WHATSAPP_API_TOKEN'); // Store token in .env
        $mediaUrl = "https://graph.facebook.com/v21.0/$mediaId";
        // Step 1: Get the direct media URL
        $response = Http::withToken($whatsappToken)->get($mediaUrl);
        if ($response->failed()) {
            Log::error("Failed to get media URL for ID: $mediaId, $mediaUrl");
            return null;
        }

        $mediaDirectUrl = $response->json()['url'] ?? null;

        if ($mediaDirectUrl) {
            // Step 2: Download the actual media file
            $mediaData = Http::withToken($whatsappToken)->get($mediaDirectUrl);
            if ($mediaData->failed()) {
                Log::error("Failed to download media from: $mediaDirectUrl, $mediaUrl");
                return null;
            }

            // Get file extension based on content type
            $contentType = $mediaData->header('Content-Type');
            $extension = $this->getExtensionFromMimeType($contentType);

            // Save file
            $filename = uniqid() . $extension;
            Storage::disk('public')->put($filename, $mediaData->body());
            $file_type = explode('/',$contentType)[0];
            $ftype = $file_type == 'application' ? 'file' : $file_type;
            $media_id = Media::create([
                'url' => $filename,
                'type' => $ftype,
                'name' => $filename
            ]);

            return $media_id->id;
        }

        return null;
    }

    /**
     * Function to get the file extension from MIME type
     */
    function getExtensionFromMimeType($mimeType)
    {
        $mimeMap = [
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'video/mp4' => '.mp4',
            'audio/mpeg' => '.mp3',
            'audio/ogg' => '.mp3',
            'application/pdf' => '.pdf',
            'application/vnd.ms-excel' => '.xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
        ];

        return $mimeMap[$mimeType] ?? '';
    }
}
