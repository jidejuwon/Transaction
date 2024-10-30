<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function createTransaction(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:credit,debit'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $user = User::where('id', $request->user_id)->lockForUpdate()->first();

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
                    'current_balance' => $newBalance,
                    'reference' => $reference
                ]);

                return response()->json(['transaction' => $transaction], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create transaction. Please try again.'], 500);
        }

    }

    public function getBalance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        return response()->json(['balance' => $user->balance]);
    }
}
