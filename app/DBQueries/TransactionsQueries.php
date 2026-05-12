<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class TransactionsQueries
{
    /**
     * Get complete transaction history (admin view – all users).
     * expiry_date is now on stock_ins, not supplies.
     */
    public static function getAllTransactions()
    {
        return DB::select("
            SELECT
                st.transaction_id,
                st.transaction_date,
                st.transaction_type,
                s.supply_name,
                c.category_name,
                st.quantity,
                s.unit_measure,
                CONCAT(u.first_name, ' ', u.last_name) AS performed_by,
                sup.supplier_name,
                si.cost,
                si.receipt_no,
                si.delivery_date,
                si.expiry_date,
                so.purpose,
                so.remarks,
                CONCAT(ua.first_name, ' ', ua.last_name) AS approved_by,
                so.approved_at
            FROM stock_transactions st
            JOIN supplies    s   ON st.supply_id       = s.supply_id
            JOIN categories  c   ON s.category_id      = c.category_id
            JOIN users       u   ON st.user_id          = u.user_id
            LEFT JOIN stock_ins  si  ON st.transaction_id = si.transaction_id
            LEFT JOIN suppliers  sup ON si.supplier_id    = sup.supplier_id
            LEFT JOIN stock_outs so  ON st.transaction_id = so.transaction_id
            LEFT JOIN users      ua  ON so.approved_by    = ua.user_id
            ORDER BY st.transaction_date DESC
        ");
    }

    /**
     * Get transaction history for a specific user (staff view – only their own).
     * expiry_date is now on stock_ins, not supplies.
     */
    public static function getUserTransactions(int $userId)
    {
        return DB::select("
            SELECT
                st.transaction_id,
                st.transaction_date,
                st.transaction_type,
                s.supply_name,
                c.category_name,
                st.quantity,
                s.unit_measure,
                CONCAT(u.first_name, ' ', u.last_name) AS performed_by,
                sup.supplier_name,
                si.cost,
                si.receipt_no,
                si.delivery_date,
                si.expiry_date,
                so.purpose,
                so.remarks,
                CONCAT(ua.first_name, ' ', ua.last_name) AS approved_by,
                so.approved_at
            FROM stock_transactions st
            JOIN supplies    s   ON st.supply_id       = s.supply_id
            JOIN categories  c   ON s.category_id      = c.category_id
            JOIN users       u   ON st.user_id          = u.user_id
            LEFT JOIN stock_ins  si  ON st.transaction_id = si.transaction_id
            LEFT JOIN suppliers  sup ON si.supplier_id    = sup.supplier_id
            LEFT JOIN stock_outs so  ON st.transaction_id = so.transaction_id
            LEFT JOIN users      ua  ON so.approved_by    = ua.user_id
            WHERE st.user_id = ?
            ORDER BY st.transaction_date DESC
        ", [$userId]);
    }

    /**
     * Current inventory status for all supplies.
     * Removed expiry_date and batch_no — those no longer exist on supplies.
     */
    public static function getInventoryStatus()
    {
        return DB::select("
            SELECT
                s.supply_id,
                s.supply_name,
                s.supply_img_path,
                c.category_name,
                s.unit_measure,
                s.current_stock,
                s.reorder_level,
                s.status AS supply_status,
                CASE
                    WHEN s.current_stock = 0               THEN 'Out of Stock'
                    WHEN s.current_stock <= s.reorder_level THEN 'Low Stock'
                    ELSE 'Sufficient'
                END AS stock_alert
            FROM supplies s
            JOIN categories c ON s.category_id = c.category_id
            WHERE s.status != 'inactive'
            ORDER BY s.current_stock ASC
        ");
    }

    /**
     * Low stock supplies (at or below reorder level).
     */
    public static function getLowStockSupplies()
    {
        return DB::select("
            SELECT
                s.supply_name,
                c.category_name,
                s.current_stock,
                s.reorder_level,
                (s.reorder_level - s.current_stock) AS shortage_quantity,
                s.unit_measure
            FROM supplies s
            JOIN categories c ON s.category_id = c.category_id
            WHERE s.current_stock <= s.reorder_level
              AND s.status = 'active'
            ORDER BY shortage_quantity DESC
        ");
    }

    /**
     * Most consumed supplies (stock-out analysis).
     */
    public static function getMostConsumedSupplies()
    {
        return DB::select("
            SELECT
                s.supply_name,
                s.unit_measure,
                SUM(st.quantity)                                           AS total_quantity_consumed,
                COUNT(st.transaction_id)                                   AS number_of_usage,
                ROUND(SUM(st.quantity) / COUNT(st.transaction_id), 2)     AS average_per_usage
            FROM stock_transactions st
            JOIN supplies s ON st.supply_id = s.supply_id
            WHERE st.transaction_type = 'stock_out'
            GROUP BY s.supply_id, s.supply_name, s.unit_measure
            ORDER BY total_quantity_consumed DESC
        ");
    }

    /**
     * Total purchases per supplier (stock-in summary).
     * suppliers table has no status column — no filter applied.
     */
    public static function getSupplierPurchasesSummary()
    {
        return DB::select("
            SELECT
                sup.supplier_id,
                sup.supplier_name,
                COUNT(si.transaction_id)       AS total_stock_in,
                SUM(si.cost)                   AS total_amount_spent,
                ROUND(AVG(si.cost), 2)         AS avg_transaction_cost
            FROM suppliers sup
            JOIN stock_ins si ON sup.supplier_id = si.supplier_id
            GROUP BY sup.supplier_id, sup.supplier_name
            ORDER BY total_amount_spent DESC
        ");
    }

    /**
     * Stock status check — mirrors fn_get_stock_status stored function.
     */
    public static function getStockStatus(int $supplyId): string
    {
        $result = DB::selectOne("
            SELECT current_stock, reorder_level
            FROM supplies
            WHERE supply_id = ?
        ", [$supplyId]);

        if (!$result)                                       return 'Not Found';
        if ($result->current_stock == 0)                   return 'Out of Stock';
        if ($result->current_stock <= $result->reorder_level) return 'Low Stock';
        return 'Sufficient';
    }
}