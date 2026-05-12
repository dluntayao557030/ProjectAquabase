<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DBQueries\DashboardQueries;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $totalSupplies       = DashboardQueries::getTotalSupplies();
        $suppliesAddedToday  = DashboardQueries::getSuppliesAddedToday($today);
        $suppliesUsedToday   = DashboardQueries::getSuppliesUsedToday($today);
        $categoryData        = collect(DashboardQueries::getCategoryData());
        $lowStockSupplies    = collect(DashboardQueries::getLowStockSupplies());

        return view('admin_dashboard.index', compact(
            'totalSupplies',
            'suppliesAddedToday',
            'suppliesUsedToday',
            'categoryData',
            'lowStockSupplies'
        ));
    }

    public function kpiInventory()
    {
        $data = DashboardQueries::getAllSuppliesForKpi();
        return response()->json($data ?: []);
    }

    public function kpiStockIn()
    {
        $today = Carbon::now('Asia/Manila')->toDateString();
        $data = DashboardQueries::getStockInToday($today);
        return response()->json($data ?: []);
    }

    public function kpiStockOut()
    {
        $today = Carbon::now('Asia/Manila')->toDateString();
        $data = DashboardQueries::getStockOutToday($today);
        return response()->json($data ?: []);
    }
}