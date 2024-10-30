<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'amount' => 'required|numeric|gt:0',
            'type' => 'required|in:credit,debit'
        ]);

        if($validator->fails())
            return response()->json(['error' => $validator->errors()->first()], 400);

        try {
            return DB::transaction(function () use ($request) {
                $user = User::where('email', $request->email)->lockForUpdate()->first();

                $balance = $user->balance;
                $amount = $request->amount;
                $transactionType = $request->type;

                if($transactionType === 'debit' && $user->balance < $amount )
                    return response()->json(['error' => 'Insufficient funds'], 400);

                $previous_balance = $balance;

                $newBalance = $transactionType === 'credit'
                    ? $previous_balance + $amount
                    : $previous_balance - $amount;

                $reference = Str::uuid();

                $user->balance = $newBalance;
                $user->save();

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => $transactionType,
                    'previous_balance' => $previous_balance,
                    'current_balance' => round($newBalance, 2),
                    'reference' => $reference
                ]);

                return response()->json(['transaction' => $transaction], 201);
            });
        } catch (\Exception $e) {
            Log::error('Transaction creation failed due to an unexpected error.', [
                'email' => $request->email,
                'amount' => $request->amount,
                'type' => $request->type,
                'error_message' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to create transaction. Please try again.'], 500);
        }

    }

    public function getBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if($validator->fails())
            return response()->json(['error' => "Invalid User"], 400);

        $user = User::where('email', $request->email)->first();
        return response()->json(['balance' => $user->getBalance()], 200);
    }
}
