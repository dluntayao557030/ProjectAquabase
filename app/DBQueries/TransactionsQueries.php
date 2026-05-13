<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class TransactionsQueries
{
    /**
     * Get all transactions – uses vw_transaction_history.
     * Filters can be applied in PHP if needed.
     */
    public static function getAllTransactions()
    {
        return DB::table('vw_transaction_history')
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get transactions for a specific user – uses the same view with WHERE user_id.
     */
    public static function getUserTransactions(int $userId)
    {
        return DB::table('vw_transaction_history')
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Current inventory status – uses vw_inventory_status.
     */
    public static function getInventoryStatus()
    {
        return DB::table('vw_inventory_status')
            ->orderBy('current_stock', 'asc')
            ->get();
    }

    /**
     * Low stock supplies – uses vw_low_stock_supplies.
     */
    public static function getLowStockSupplies()
    {
        return DB::table('vw_low_stock_supplies')
            ->orderBy('shortage_quantity', 'desc')
            ->get();
    }

    /**
     * Most consumed supplies – uses vw_most_consumed_supplies.
     */
    public static function getMostConsumedSupplies()
    {
        return DB::table('vw_most_consumed_supplies')
            ->orderBy('total_quantity_consumed', 'desc')
            ->get();
    }

    /**
     * Supplier purchases summary – uses vw_supplier_purchases_summary.
     */
    public static function getSupplierPurchasesSummary()
    {
        return DB::table('vw_supplier_purchases_summary')
            ->orderBy('total_amount_spent', 'desc')
            ->get();
    }

    /**
     * Stock status – uses stored function (kept as is).
     */
    public static function getStockStatus(int $supplyId): string
    {
        $result = DB::selectOne("SELECT fn_get_stock_status(?) AS status", [$supplyId]);
        return $result ? $result->status : 'Not Found';
    }

    /**
     * Supply transactions by date range – uses stored procedure (kept as is).
     */
    public static function getSupplyTransactionsByDateRange(int $supplyId, string $startDate, string $endDate): array
    {
        return DB::select('CALL sp_get_supply_transactions(?, ?, ?)', [$supplyId, $startDate, $endDate]);
    }
}