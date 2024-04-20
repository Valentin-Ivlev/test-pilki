<?php

namespace App\Services;

use App\Models\LoyaltyAccount;
use App\Models\LoyaltyPointsTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoyaltyPointsReceived;

class LoyaltyAccountService
{
    public function depositPoints($data)
    {
        Log::info("Attempting to deposit points: ", $data);
        $account = LoyaltyAccount::where($data['account_type'], '=', $data['account_id'])->first();

        if (!$account || !$account->active) {
            Log::error('Account not found or not active', ['account_type' => $data['account_type'], 'account_id' => $data['account_id']]);
            return false;
        }

        $transaction = LoyaltyPointsTransaction::performPaymentLoyaltyPoints(
            $account->id,
            $data['loyalty_points_rule'],
            $data['description'],
            $data['payment_id'],
            $data['payment_amount'],
            $data['payment_time']
        );

        $this->notifyAccount($account, $transaction);
        Log::info("Deposit completed successfully.", ['transaction' => $transaction]);

        return $transaction;
    }

    public function withdrawPoints($data)
    {
        $account = LoyaltyAccount::where($data['account_type'], '=', $data['account_id'])->first();

        if (!$account || !$account->active || $account->getBalance() < $data['points_amount']) {
            Log::info('Account not found, not active or insufficient funds');
            return false;
        }

        return LoyaltyPointsTransaction::withdrawLoyaltyPoints($account->id, $data['points_amount'], $data['description']);
    }

    public function cancelTransaction($transactionId, $reason)
    {
        $transaction = LoyaltyPointsTransaction::where('id', $transactionId)->where('canceled', 0)->first();

        if (!$transaction) {
            return false;
        }

        $transaction->canceled = time();
        $transaction->cancellation_reason = $reason;
        $transaction->save();

        return true;
    }

    private function notifyAccount($account, $transaction)
    {
        if ($account->email != '' && $account->email_notification) {
            Mail::to($account->email)->send(new LoyaltyPointsReceived($transaction->points_amount, $account->getBalance()));
        }
        if ($account->phone != '' && $account->phone_notification) {
            $this->sendSmsNotification($account->phone, $transaction->points_amount, $account->getBalance());
        }
    }

    private function sendSmsNotification($phone, $pointsAmount, $balance)
    {
        Log::info("SMS to $phone: Received $pointsAmount points. Your new balance is $balance.");
    }
}