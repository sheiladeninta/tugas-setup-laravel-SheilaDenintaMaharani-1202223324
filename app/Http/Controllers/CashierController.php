<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = Product::orderBy('name')->get();
        $transactions = Transaction::with('items.product')
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return view('cashier.index', compact('products', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $totalItems = 0;
        foreach ($request->items as $item) {
            $totalItems += $item['quantity'];
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'customer_name' => $request->customer_name,
                'total_items' => $totalItems,
                'subtotal' => $request->subtotal,
                'tax' => $request->tax,
                'total_amount' => $request->total_amount,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);
            }

            DB::commit();

            $transaction->load('items.product');
            $transaction->user_name = Auth::user()->name;

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan',
                'transaction' => $transaction,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getProducts()
    {
        $products = Product::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function getTransactions()
    {
        $transactions = Transaction::with('items.product')
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }
}
