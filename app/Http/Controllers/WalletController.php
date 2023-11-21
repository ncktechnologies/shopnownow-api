<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;


class WalletController extends Controller
{
    public function balance()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Return the user's balance and loyalty points
            return response()->json(['wallet_balance' => $user->wallet, 'loyalty_points' => $user->loyalty_points]);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function fundWallet(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            // Handle validation errors or unexpected errors
            return response()->json(['error' => $e->getMessage()], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    public function withdrawFunds(Request $request)
    {
        try {
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
            $user->wallet -= $request->amount;
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
        } catch (\Exception $e) {
            // Handle validation errors or unexpected errors
            return response()->json(['error' => $e->getMessage()], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    public function convertPoints()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Check if the user has any points
            if ($user->loyalty_points <= 0) {
                return response()->json(['message' => 'No points to convert'], 400);
            }

            // Convert all points to cash
            $amount = $user->loyalty_points;

            // Update the user's balance and reset points
            $user->wallet += $amount;
            $user->loyalty_points = 0;
            $user->save();

            // Create a new transaction
            $transaction = new Transaction([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'message' => 'Converted ' . $user->loyalty_points . ' points to cash',
            ]);
            $transaction->save();

            return response()->json(['message' => 'Points converted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while converting points', 'error' => $e->getMessage()], 500);
        }
    }

    public function transactionHistory()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Get the user's transactions
            $transactions = Transaction::where('user_id', $user->id)->get();

            return response()->json(['transactions' => $transactions]);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function transactionDetails($id)
    {
        try {
            // Get the transaction
            $transaction = Transaction::findOrFail($id);

            // Check if the transaction belongs to the authenticated user
            if ($transaction->user_id != Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return response()->json(['transaction' => $transaction]);
        } catch (\Exception $e) {
            // Handle not found error or unexpected errors
            return response()->json(['error' => $e->getMessage()], $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500);
        }
    }
}
