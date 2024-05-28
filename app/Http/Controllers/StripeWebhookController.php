<?php

namespace App\Http\Controllers;

use App\Helper\StripeHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function endpoint(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $payload['type'];
        switch ($event) {
            case 'charge.captured':
                $status = StripeHelper::handleChargeCapture($payload);
                if (!$status) {
                    return response()->json(['success' => false, 'message' => 'Unable to handle capture success'], 500);
                }
                return response()->json(['success' => true, 'message' => 'Capture success handled']);
            default:
                return response()->json(['success' => false, 'message' => 'Unhandled event ' . $event]);
        }
    }
}
