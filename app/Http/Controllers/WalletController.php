<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function balance()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Return the user's balance and loyalty points
        return response()->json(['wallet_balance' => $user->wallet, 'loyalty_points' => $user->loyalty_points]);
    }

    public function fundWallet(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Update the user's balance
        $user->wallet += $request->amount;
        $user->save();

        // Create a new transaction
        $transaction = new Transaction([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'credit',
            'reference' => $request->reference,
            'status' => 'success',
            'message' => 'Wallet funding',
        ]);
        $transaction->save();

        return response()->json(['message' => 'Wallet funded successfully']);
    }

    public function withdrawFunds(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has enough balance
        if ($user->wallet < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        // Update the user's balance
        $user->balance -= $request->amount;
        $user->save();

        // Create a new transaction
        $transaction = new Transaction([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'reference' => $request->reference,
            'status' => 'success',
            'type' => 'debit',
            'message' => 'Wallet withdrawal',
        ]);
        $transaction->save();

        return response()->json(['message' => 'Withdrawal successful']);
    }

    public function transactionHistory()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the user's transactions
        $transactions = Transaction::where('user_id', $user->id)->get();

        return response()->json(['transactions' => $transactions]);
    }

    public function transactionDetails($id)
    {
        // Get the transaction
        $transaction = Transaction::findOrFail($id);

        // Check if the transaction belongs to the authenticated user
        if ($transaction->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['transaction' => $transaction]);
    }
}
