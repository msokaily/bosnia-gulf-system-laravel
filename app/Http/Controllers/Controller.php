<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function resJson($data, $status = true)
    {
        return response()->json([
            'status' => $status,
            'data' => $data
        ], $status ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
