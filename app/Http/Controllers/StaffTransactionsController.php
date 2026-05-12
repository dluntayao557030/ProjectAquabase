<?php

namespace App\Http\Controllers;

use App\DBQueries\TransactionsQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StaffTransactionsController extends Controller
{
    /**
     * Display staff transactions dashboard.
     */
        public function index()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $inventory = collect(TransactionsQueries::getInventoryStatus());
        // Sort: Out of Stock → Low Stock → Sufficient
        $inventory = $inventory->sortBy(function ($item) {
            static $order = ['Out of Stock' => 1, 'Low Stock' => 2, 'Sufficient' => 3];
            return $order[$item->stock_alert] ?? 3;
        })->values();

        $lowStock     = TransactionsQueries::getLowStockSupplies();
        $mostConsumed = TransactionsQueries::getMostConsumedSupplies();
        $transactions = TransactionsQueries::getUserTransactions($userId);

        // Fetch active suppliers for the Stock In dropdown
        $suppliers = DB::table('suppliers')
            ->where('status', 'active')
            ->orderBy('supplier_name')
            ->get(['supplier_id', 'supplier_name']);

        return view('staff_transactions.index', compact(
            'inventory',
            'lowStock',
            'mostConsumed',
            'transactions',
            'suppliers'
        ));
    }

    /**
     * Stock In – staff can add stock.
     */
        public function stockIn(Request $request)
    {
        $request->validate([
            'supply_id'    => 'required|exists:supplies,supply_id',
            'supplier_id'  => 'nullable|exists:suppliers,supplier_id',
            'quantity'     => 'required|integer|min:1',
            'unit_cost'    => 'required|numeric|min:0',
            'expiry_date'  => 'nullable|date',         
            'receipt_no'   => 'nullable|string|max:100',
            'delivery_date'=> 'nullable|date',
            'remarks'      => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            DB::table('supplies')->where('supply_id', $request->supply_id)
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
                'supplier_id'    => $request->supplier_id,
                'delivery_date'  => $request->delivery_date ?? now(),
                'cost'           => $request->unit_cost,
                'expiry_date'    => $request->expiry_date,   // <-- new
                'receipt_no'     => $request->receipt_no,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::commit();

            return redirect()->route('staff.transactions.index')
                ->with('success', "Stock In: +{$request->quantity} units added.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('staff.transactions.index')
                ->with('error', 'Stock In failed. Please try again.');
        }
    }

    /**
     * Stock Out – staff can deduct stock.
     */
    public function stockOut(Request $request)
    {
        $request->validate([
            'supply_id' => 'required|exists:supplies,supply_id',
            'quantity'  => 'required|integer|min:1',
            'purpose'   => 'nullable|string|max:255',
            'remarks'   => 'nullable|string|max:255',
        ]);

        $supply = DB::table('supplies')->where('supply_id', $request->supply_id)->first();
        if (!$supply || $supply->current_stock < $request->quantity) {
            return redirect()->route('staff.transactions.index')
                ->with('error', "Insufficient stock. Only {$supply->current_stock} units available.");
        }

        DB::beginTransaction();
        try {
            DB::table('supplies')->where('supply_id', $request->supply_id)
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
                'purpose'        => $request->purpose,
                'remarks'        => $request->remarks,
                'approved_at'    => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::commit();

            return redirect()->route('staff.transactions.index')
                ->with('success', "Stock Out: -{$request->quantity} units deducted.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('staff.transactions.index')
                ->with('error', 'Stock Out failed. Please try again.');
        }
    }

    /**
     * AJAX endpoint – return only current staff’s transactions.
     */
    public function historyData()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json([]);
        }
        $transactions = TransactionsQueries::getUserTransactions($userId);
        return response()->json($transactions);
    }
}