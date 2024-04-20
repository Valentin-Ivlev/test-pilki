<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoyaltyPointRequest;
use App\Services\LoyaltyAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LoyaltyPointsController extends Controller
{
    private $loyaltyAccountService;

    public function __construct(LoyaltyAccountService $loyaltyAccountService)
    {
        $this->loyaltyAccountService = $loyaltyAccountService;
    }

    public function deposit(LoyaltyPointRequest $request): JsonResponse
    {
        $transaction = $this->loyaltyAccountService->depositPoints($request->validated());

        return $transaction
            ? response()->json($transaction, Response::HTTP_OK)
            : response()->json(['message' => 'Unable to process deposit'], Response::HTTP_BAD_REQUEST);
    }

    public function cancel(LoyaltyPointRequest $request): JsonResponse
    {
        $success = $this->loyaltyAccountService->cancelTransaction($request->input('transaction_id'), $request->input('cancellation_reason'));

        return $success
            ? response()->json(['message' => 'Transaction cancelled successfully'], Response::HTTP_OK)
            : response()->json(['message' => 'Transaction not found or already cancelled'], Response::HTTP_NOT_FOUND);
    }

    public function withdraw(LoyaltyPointRequest $request): JsonResponse
    {
        $transaction = $this->loyaltyAccountService->withdrawPoints($request->validated());

        return $transaction
            ? response()->json($transaction, Response::HTTP_OK)
            : response()->json(['message' => 'Insufficient funds or account not active'], Response::HTTP_BAD_REQUEST);
    }
}