<?php

namespace App\Http\Controllers;

use App\Services\PaymongoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymongoWebhookController extends Controller
{
    public function __construct(
        protected PaymongoService $paymongoService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $result = $this->paymongoService->handleWebhook(
            $request->getContent(),
            $request->header('Paymongo-Signature')
                ?? $request->header('X-Paymongo-Signature')
                ?? $request->header('paymongo-signature')
        );

        Log::info('Paymongo webhook handled', [
            'result' => $result,
            'result_status' => $result['status'],
            'result_body' => $result['body'],
        ]);

        return response()->json($result['body'], $result['status']);
    }
}
