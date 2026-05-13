<?php

namespace App\Http\Controllers;

use App\DBQueries\TransactionsQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StaffTransactionsController extends Controller
{
    public function index()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $inventory = collect(TransactionsQueries::getInventoryStatus())
            ->sortBy(function ($item) {
                static $order = ['Out of Stock' => 1, 'Low Stock' => 2, 'Sufficient' => 3];
                return $order[$item->stock_alert] ?? 3;
            })->values();

        $lowStock     = TransactionsQueries::getLowStockSupplies();
        $mostConsumed = TransactionsQueries::getMostConsumedSupplies();
        $transactions = TransactionsQueries::getUserTransactions($userId);

        $suppliers = DB::table('suppliers')
            ->where('status', 'active')
            ->orderBy('supplier_name')
            ->get(['supplier_id', 'supplier_name']);

        return view('staff_transactions.index', compact(
            'inventory', 'lowStock', 'mostConsumed', 'transactions', 'suppliers'
        ));
    }

    /**
     * Stock In – calls stored procedure sp_stock_in.
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

        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        try {
            DB::statement('CALL sp_stock_in(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->supply_id,
                $userId,
                $request->quantity,
                $request->unit_cost,
                $request->supplier_id ?? 0,      // pass 0 for NULL
                $request->expiry_date,
                $request->receipt_no,
                $request->delivery_date,
                $request->remarks
            ]);

            return redirect()->route('staff.transactions.index')
                ->with('success', "Stock In: +{$request->quantity} units added.");
        } catch (\Exception $e) {
            return redirect()->route('staff.transactions.index')
                ->with('error', 'Stock In failed: ' . $e->getMessage());
        }
    }

    /**
     * Stock Out – calls stored procedure sp_stock_out.
     */
    public function stockOut(Request $request)
    {
        $request->validate([
            'supply_id' => 'required|exists:supplies,supply_id',
            'quantity'  => 'required|integer|min:1',
            'purpose'   => 'nullable|string|max:255',
            'remarks'   => 'nullable|string|max:255',
        ]);

        $userId = Session::get('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        try {
            DB::statement('CALL sp_stock_out(?, ?, ?, ?, ?)', [
                $request->supply_id,
                $userId,
                $request->quantity,
                $request->purpose,
                $request->remarks
            ]);

            return redirect()->route('staff.transactions.index')
                ->with('success', "Stock Out: -{$request->quantity} units deducted.");
        } catch (\Exception $e) {
            return redirect()->route('staff.transactions.index')
                ->with('error', 'Stock Out failed: ' . $e->getMessage());
        }
    }

    public function historyData()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json([]);
        }
        return response()->json(TransactionsQueries::getUserTransactions($userId));
    }
}