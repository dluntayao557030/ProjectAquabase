<?php

namespace App\Http\Controllers;

use App\DBQueries\ReportsQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminReportsController extends Controller
{
    public function index()
    {
        $categories = ReportsQueries::getAllCategories();
        $supplies   = ReportsQueries::getAllSupplies();
        $staffList  = ReportsQueries::getAllStaff();

        return view('admin_reports.index', compact('categories', 'supplies', 'staffList'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('report_type');

        return match ($type) {
            'stock_movement'    => $this->stockMovement($request),
            'current_inventory' => $this->currentInventory($request),
            'low_stock'         => $this->lowStock($request),
            'purchases_supplier'=> $this->purchasesPerSupplier($request),
            'most_consumed'     => $this->mostConsumed($request),
            'supply_transactions'=> $this->supplyTransactions($request),
            default             => response()->json(['rows' => [], 'summary' => []]),
        };
    }

    private function stockMovement(Request $request)
    {
        $rows = collect(ReportsQueries::getStockMovement([
            'date_from'          => $request->date_from,
            'date_to'            => $request->date_to,
            'delivery_date_from' => $request->delivery_date_from,
            'delivery_date_to'   => $request->delivery_date_to,
            'txn_type'           => $request->txn_type,
            'category'           => $request->category,
            'supply'             => $request->supply,
            'user'               => $request->user,
        ]));

        $totalIn  = $rows->where('transaction_type', 'stock_in')->sum('quantity');
        $totalOut = $rows->where('transaction_type', 'stock_out')->sum('quantity');
        $totalCost = $rows->where('transaction_type', 'stock_in')
                          ->filter(fn($r) => $r->cost !== null)
                          ->sum(fn($r) => (float) $r->cost);

        return response()->json([
            'rows'    => $rows,
            'summary' => [
                'Total Transactions' => $rows->count(),
                'Total Stock In'     => $totalIn,
                'Total Stock Out'    => $totalOut,
                'Net Movement'       => $totalIn - $totalOut,
                'Total Cost (In)'    => '₱' . number_format($totalCost, 2),
            ],
        ]);
    }

    private function currentInventory(Request $request)
    {
        $rows = collect(ReportsQueries::getCurrentInventory([
            'category'     => $request->category,
            'stock_status' => $request->stock_status,
        ]));

        return response()->json([
            'rows'    => $rows,
            'summary' => [
                'Total Supplies'  => $rows->count(),
                'Total Stock'     => $rows->sum('current_stock'),
                'Low Stock Items' => $rows->where('stock_alert', 'Low Stock')->count(),
                'Out of Stock'    => $rows->where('stock_alert', 'Out of Stock')->count(),
                'Sufficient'      => $rows->where('stock_alert', 'Sufficient')->count(),
            ],
        ]);
    }

    private function lowStock(Request $request)
    {
        $rows = collect(ReportsQueries::getLowStockSupplies([
            'category' => $request->category,
        ]));

        return response()->json([
            'rows'    => $rows,
            'summary' => [
                'Items to Restock'   => $rows->count(),
                'Out of Stock'       => $rows->where('stock_status', 'Out of Stock')->count(),
                'Total Units Needed' => $rows->sum('shortage_quantity'),
            ],
        ]);
    }

    private function purchasesPerSupplier(Request $request)
    {
        $rows = collect(ReportsQueries::getPurchasesPerSupplier([
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
        ]));

        return response()->json([
            'rows'    => $rows,
            'summary' => [
                'Total Suppliers'   => $rows->count(),
                'Total Stock Ins'   => $rows->sum('total_stock_in'),
                'Total Amount Spent'=> '₱' . number_format($rows->sum('total_amount_spent'), 2),
            ],
        ]);
    }

    private function mostConsumed(Request $request)
    {
        $rows = collect(ReportsQueries::getMostConsumedSupplies([
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
            'category'  => $request->category,
        ]));

        return response()->json([
            'rows'    => $rows,
            'summary' => [
                'Supplies Tracked' => $rows->count(),
                'Total Consumed'   => $rows->sum('total_quantity_consumed'),
                'Total Usages'     => $rows->sum('number_of_usage'),
            ],
        ]);
    }
}