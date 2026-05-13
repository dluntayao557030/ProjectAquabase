<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class ReportsQueries
{
    // SHARED HELPERS (unchanged)
    public static function getAllCategories(): array
    {
        return DB::select('SELECT category_id, category_name FROM categories ORDER BY category_name ASC');
    }

    public static function getAllSupplies(): array
    {
        return DB::select('SELECT supply_id, supply_name FROM supplies ORDER BY supply_name ASC');
    }

    public static function getAllStaff(): array
    {
        return DB::select(
            "SELECT user_id, CONCAT(first_name, ' ', last_name) AS full_name
             FROM users WHERE role = 'staff' ORDER BY first_name ASC"
        );
    }

    /**
     * REPORT 1 – STOCK MOVEMENT
     * Uses vw_stock_movement view and applies filters in PHP.
     */
    public static function getStockMovement(array $f = []): array
    {
        $query = DB::table('vw_stock_movement');

        if (!empty($f['date_from'])) {
            $query->whereDate('transaction_date', '>=', $f['date_from']);
        }
        if (!empty($f['date_to'])) {
            $query->whereDate('transaction_date', '<=', $f['date_to']);
        }
        if (!empty($f['delivery_date_from'])) {
            $query->whereDate('delivery_date', '>=', $f['delivery_date_from']);
        }
        if (!empty($f['delivery_date_to'])) {
            $query->whereDate('delivery_date', '<=', $f['delivery_date_to']);
        }
        if (!empty($f['txn_type'])) {
            $query->where('transaction_type', $f['txn_type']);
        }
        if (!empty($f['category'])) {
            $query->where('category_id', $f['category']);
        }
        if (!empty($f['supply'])) {
            $query->where('supply_id', $f['supply']);
        }
        if (!empty($f['user'])) {
            $query->where('user_id', $f['user']);
        }

        return $query->orderBy('transaction_date', 'desc')->get()->toArray();
    }

    /**
     * REPORT 2 – CURRENT INVENTORY
     * Uses vw_current_inventory view.
     */
    public static function getCurrentInventory(array $f = []): array
    {
        $query = DB::table('vw_current_inventory');

        if (!empty($f['category'])) {
            $query->where('category_id', $f['category']);
        }
        if (!empty($f['stock_status'])) {
            match ($f['stock_status']) {
                'sufficient'   => $query->where('stock_alert', 'Sufficient'),
                'low_stock'    => $query->where('stock_alert', 'Low Stock'),
                'out_of_stock' => $query->where('stock_alert', 'Out of Stock'),
                default        => null,
            };
        }

        return $query->orderBy('current_stock', 'asc')->get()->toArray();
    }

    /**
     * REPORT 3 – LOW STOCK MONITORING
     * Uses vw_low_stock_supplies view (same as TransactionsQueries).
     */
    public static function getLowStockSupplies(array $f = []): array
    {
        $query = DB::table('vw_low_stock_supplies');

        if (!empty($f['category'])) {
            $query->where('category_id', $f['category']);
        }

        $results = $query->orderBy('shortage_quantity', 'desc')->get();

        // Add stock_status column to match original return structure
        return $results->map(function ($item) {
            $item->stock_status = $item->current_stock == 0 ? 'Out of Stock' : 'Low Stock';
            return $item;
        })->toArray();
    }

    /**
     * REPORT 4 – TOTAL PURCHASES PER SUPPLIER (with optional date filter)
     * Since aggregation is needed with date filtering, we use the base vw_purchases_per_supplier
     * and then manually aggregate in PHP (or we could keep the original raw query for performance).
     * For clarity, I'll keep the original efficient SQL query instead of a view, because views cannot accept date parameters.
     * This is the only method that still uses raw SQL due to dynamic date aggregation.
     */
    public static function getPurchasesPerSupplier(array $f = []): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($f['date_from'])) {
            $where[]  = 'DATE(st.transaction_date) >= ?';
            $params[] = $f['date_from'];
        }
        if (!empty($f['date_to'])) {
            $where[]  = 'DATE(st.transaction_date) <= ?';
            $params[] = $f['date_to'];
        }

        $w = implode(' AND ', $where);

        return DB::select(
            "SELECT sup.supplier_name,
                    COUNT(si.transaction_id)       AS total_stock_in,
                    SUM(si.cost)                   AS total_amount_spent,
                    ROUND(AVG(si.cost), 2)         AS avg_transaction_cost
             FROM suppliers sup
             JOIN stock_ins si          ON sup.supplier_id    = si.supplier_id
             JOIN stock_transactions st ON si.transaction_id  = st.transaction_id
             WHERE {$w}
             GROUP BY sup.supplier_id, sup.supplier_name
             ORDER BY total_amount_spent DESC",
            $params
        );
    }

    /**
     * REPORT 5 – MOST CONSUMED SUPPLIES
     * Uses vw_most_consumed_supplies view, but needs date/user/category filters.
     * Since the view is pre-aggregated, we cannot apply date filters after aggregation.
     * Therefore we keep the original raw query for this report as well.
     * (Alternatively, we could create a more detailed view without aggregation, but that would be less efficient.)
     */
    public static function getMostConsumedSupplies(array $f = []): array
    {
        $where  = ["st.transaction_type = 'stock_out'"];
        $params = [];

        if (!empty($f['date_from'])) {
            $where[]  = 'DATE(st.transaction_date) >= ?';
            $params[] = $f['date_from'];
        }
        if (!empty($f['date_to'])) {
            $where[]  = 'DATE(st.transaction_date) <= ?';
            $params[] = $f['date_to'];
        }
        if (!empty($f['category'])) {
            $where[]  = 's.category_id = ?';
            $params[] = $f['category'];
        }
        if (!empty($f['user_id'])) {
            $where[]  = 'st.user_id = ?';
            $params[] = $f['user_id'];
        }

        $w = implode(' AND ', $where);

        return DB::select(
            "SELECT s.supply_name, s.unit_measure,
                    SUM(st.quantity)                                              AS total_quantity_consumed,
                    COUNT(st.transaction_id)                                      AS number_of_usage,
                    ROUND(SUM(st.quantity) / COUNT(st.transaction_id), 2)        AS average_per_usage
             FROM stock_transactions st
             JOIN supplies s ON st.supply_id = s.supply_id
             WHERE {$w}
             GROUP BY s.supply_id, s.supply_name, s.unit_measure
             ORDER BY total_quantity_consumed DESC",
            $params
        );
    }
}