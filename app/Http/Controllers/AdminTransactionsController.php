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

        $suppliers = DB::select("
            SELECT supplier_id, supplier_name
            FROM suppliers
            ORDER BY supplier_name ASC
        ");

        return view('admin_transactions.index', compact(
            'inventory', 'lowStock', 'mostConsumed', 'supplierSummary', 'suppliers'
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
                $request->supplier_id ?? 0,
                $request->expiry_date,
                $request->receipt_no,
                $request->delivery_date,
                $request->remarks
            ]);

            return redirect()->route('admin.transactions.index')
                ->with('success', "Stock In: +{$request->quantity} units added.");
        } catch (\Exception $e) {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Stock In failed: ' . $e->getMessage());
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

            return redirect()->route('admin.transactions.index')
                ->with('success', "Stock Out: -{$request->quantity} units deducted.");
        } catch (\Exception $e) {
            return redirect()->route('admin.transactions.index')
                ->with('error', 'Stock Out failed: ' . $e->getMessage());
        }
    }

    public function historyData()
    {
        return response()->json(TransactionsQueries::getAllTransactions());
    }
}