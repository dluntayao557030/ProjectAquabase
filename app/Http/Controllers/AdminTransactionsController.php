<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DBQueries\TransactionsQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminTransactionsController extends Controller
{
    public function index()
    {
        $inventory = collect(TransactionsQueries::getInventoryStatus())
            ->sortBy(function ($item) {
                static $order = ['Out of Stock' => 1, 'Low Stock' => 2, 'Sufficient' => 3];
                return $order[$item->stock_alert] ?? 3;
            })->values();

        $lowStock        = TransactionsQueries::getLowStockSupplies();
        $mostConsumed    = TransactionsQueries::getMostConsumedSupplies();
        $supplierSummary = TransactionsQueries::getSupplierPurchasesSummary();

        // suppliers table has no status column — fetch all, ordered by name
        $suppliers = DB::select("
            SELECT supplier_id, supplier_name
            FROM suppliers
            ORDER BY supplier_name ASC
        ");

        return view('admin_transactions.index', compact(
            'inventory',
            'lowStock',
            'mostConsumed',
            'supplierSummary',
            'suppliers'
        ));
    }

    public function stockIn(Request $request)
    {
        $request->validate([
            'supply_id'     => 'required|integer|exists:supplies,supply_id',
            'supplier_id'   => 'nullable|integer|exists:suppliers,supplier_id',
            'quantity'      => 'required|integer|min:1',
            'unit_cost'     => 'required|numeric|min:0.01',
            'receipt_no'    => 'nullable|string|max:100',
            'delivery_date' => 'nullable|date',
            'expiry_date'   => 'nullable|date|after_or_equal:today',
            'remarks'       => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            DB::table('supplies')
                ->where('supply_id', $request->supply_id)
                ->increment('current_stock', $request->quantity);

            $transactionId = DB::table('stock_transactions')->insertGetId([
                'supply_id'        => $request->supply_id,
                'user_id'          => Session::get('user_id'),
                'transaction_date' => now(),
                'quantity'         => $request->quantity,
                'transaction_type' => 'stock_in',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('stock_ins')->insert([
                'transaction_id' => $transactionId,
                'supplier_id'    => $request->supplier_id ?: null,
                'delivery_date'  => $request->delivery_date ?? now()->toDateString(),
                'cost'           => $request->unit_cost,
                'expiry_date'    => $request->expiry_date ?: null,
                'receipt_no'     => $request->receipt_no ?: null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.transactions.index')
                ->with('success', "Stock In: +{$request->quantity} unit(s) of supply #{$request->supply_id} added.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Stock In failed. Please try again.');
        }
    }

    public function stockOut(Request $request)
    {
        $request->validate([
            'supply_id' => 'required|integer|exists:supplies,supply_id',
            'quantity'  => 'required|integer|min:1',
            'purpose'   => 'nullable|string|max:255',
            'remarks'   => 'nullable|string|max:255',
        ]);

        $supply = DB::table('supplies')
            ->where('supply_id', $request->supply_id)
            ->first(['supply_id', 'supply_name', 'current_stock']);

        if (!$supply) {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Supply not found.');
        }

        if ($supply->current_stock <= 0) {
            return redirect()->route('admin.transactions.index')
                ->with('error', "\"{$supply->supply_name}\" is currently out of stock.");
        }

        if ($request->quantity > $supply->current_stock) {
            return redirect()->route('admin.transactions.index')
                ->with('error', "Cannot deduct {$request->quantity} unit(s). Only {$supply->current_stock} available for \"{$supply->supply_name}\".");
        }

        DB::beginTransaction();
        try {
            DB::table('supplies')
                ->where('supply_id', $request->supply_id)
                ->decrement('current_stock', $request->quantity);

            $transactionId = DB::table('stock_transactions')->insertGetId([
                'supply_id'        => $request->supply_id,
                'user_id'          => Session::get('user_id'),
                'transaction_date' => now(),
                'quantity'         => $request->quantity,
                'transaction_type' => 'stock_out',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('stock_outs')->insert([
                'transaction_id' => $transactionId,
                'approved_by'    => Session::get('user_id'),
                'purpose'        => $request->purpose ?: null,
                'remarks'        => $request->remarks ?: null,
                'approved_at'    => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.transactions.index')
                ->with('success', "Stock Out: -{$request->quantity} unit(s) of \"{$supply->supply_name}\" deducted.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Stock Out failed. Please try again.');
        }
    }

    public function historyData()
    {
        return response()->json(TransactionsQueries::getAllTransactions());
    }
}