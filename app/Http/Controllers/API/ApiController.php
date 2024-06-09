<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function whatsapp_callback(Request $request)
    {

        return $this->resJson(['ok' => true]);
    }
}
