<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class baseController extends Controller {
    public function sendSuccess($message, $results = []) {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!empty($results)) {
            $response['data'] = $results;
        }
        
        return response()->json($response, 200);
    }

    // Error Response Method
    public function sendError($error, $errorMessages = [], $code = 404) {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);

    }
}
