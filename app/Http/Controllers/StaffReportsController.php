<?php

namespace App\Http\Controllers;

use App\DBQueries\ReportsQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StaffReportsController extends Controller
{
    public function index()
    {
        $categories = ReportsQueries::getAllCategories();
        $supplies   = ReportsQueries::getAllSupplies();

        return view('staff_reports.index', compact('categories', 'supplies'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('report_type');

        return match ($type) {
            'current_inventory' => $this->currentInventory($request),
            'low_stock'         => $this->lowStock($request),
            'most_consumed'     => $this->mostConsumed($request),
            default             => response()->json(['rows' => [], 'summary' => []]),
        };
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

    private function mostConsumed(Request $request)
    {
        $rows = collect(ReportsQueries::getMostConsumedSupplies([
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
            'category'  => $request->category,
            'user_id'   => Session::get('user_id'),
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