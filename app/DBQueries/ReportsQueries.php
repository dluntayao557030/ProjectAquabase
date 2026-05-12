<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class ReportsQueries
{
    // ══════════════════════════════════════════════════════
    //  SHARED HELPERS
    // ══════════════════════════════════════════════════════

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

        // ══════════════════════════════════════════════════════
        //  REPORT 1 – STOCK MOVEMENT (now includes expiry_date)
        //  Admin only
        // ══════════════════════════════════════════════════════

        public static function getStockMovement(array $f = []): array
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
        if (!empty($f['delivery_date_from'])) {
            $where[]  = 'si.delivery_date >= ?';
            $params[] = $f['delivery_date_from'];
        }
        if (!empty($f['delivery_date_to'])) {
            $where[]  = 'si.delivery_date <= ?';
            $params[] = $f['delivery_date_to'];
        }
        if (!empty($f['txn_type'])) {
            $where[]  = 'st.transaction_type = ?';
            $params[] = $f['txn_type'];
        }
        if (!empty($f['category'])) {
            $where[]  = 'c.category_id = ?';
            $params[] = $f['category'];
        }
        if (!empty($f['supply'])) {
            $where[]  = 's.supply_id = ?';
            $params[] = $f['supply'];
        }
        if (!empty($f['user'])) {
            $where[]  = 'st.user_id = ?';
            $params[] = $f['user'];
        }

        $w = implode(' AND ', $where);

        $sql = "
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
                COALESCE(so.purpose, '') AS purpose,
                so.remarks AS remarks,
                so.approved_by,
                so.approved_at
            FROM stock_transactions st
            INNER JOIN supplies s ON st.supply_id = s.supply_id
            INNER JOIN categories c ON s.category_id = c.category_id
            LEFT JOIN users u ON st.user_id = u.user_id
            LEFT JOIN stock_ins si ON st.transaction_id = si.transaction_id AND st.transaction_type = 'stock_in'
            LEFT JOIN suppliers sup ON si.supplier_id = sup.supplier_id
            LEFT JOIN stock_outs so ON st.transaction_id = so.transaction_id AND st.transaction_type = 'stock_out'
            WHERE {$w}
            ORDER BY st.transaction_date DESC
        ";

        return DB::select($sql, $params);
    }

    // ══════════════════════════════════════════════════════
    //  REPORT 2 – CURRENT INVENTORY (original, no expiry)
    //  Admin + Staff
    // ══════════════════════════════════════════════════════

    public static function getCurrentInventory(array $f = []): array
    {
        $where  = ["s.status != 'inactive'"];
        $params = [];

        if (!empty($f['category'])) {
            $where[]  = 's.category_id = ?';
            $params[] = $f['category'];
        }
        if (!empty($f['stock_status'])) {
            match ($f['stock_status']) {
                'sufficient'   => ($where[] = "s.current_stock > s.reorder_level AND s.current_stock > 0"),
                'low_stock'    => ($where[] = "s.current_stock <= s.reorder_level AND s.current_stock > 0"),
                'out_of_stock' => ($where[] = "s.current_stock = 0"),
                default        => null,
            };
        }

        $w = implode(' AND ', $where);

        return DB::select(
            "SELECT s.supply_id, s.supply_name, c.category_name,
                    s.unit_measure, s.current_stock, s.reorder_level,
                    s.status AS supply_status,
                    CASE 
                        WHEN s.current_stock = 0 THEN 'Out of Stock'
                        WHEN s.current_stock <= s.reorder_level THEN 'Low Stock'
                        ELSE 'Sufficient'
                    END AS stock_alert
             FROM supplies s
             JOIN categories c ON s.category_id = c.category_id
             WHERE {$w}
             ORDER BY s.current_stock ASC",
            $params
        );
    }

    // ══════════════════════════════════════════════════════
    //  REPORT 3 – LOW STOCK MONITORING
    //  Admin + Staff
    // ══════════════════════════════════════════════════════

    public static function getLowStockSupplies(array $f = []): array
    {
        $where  = ["s.current_stock <= s.reorder_level", "s.status = 'active'"];
        $params = [];

        if (!empty($f['category'])) {
            $where[]  = 's.category_id = ?';
            $params[] = $f['category'];
        }

        $w = implode(' AND ', $where);

        return DB::select(
            "SELECT s.supply_name, c.category_name, s.current_stock,
                    s.reorder_level, s.unit_measure,
                    (s.reorder_level - s.current_stock) AS shortage_quantity,
                    CASE 
                        WHEN s.current_stock = 0 THEN 'Out of Stock'
                        WHEN s.current_stock <= s.reorder_level THEN 'Low Stock'
                        ELSE 'Sufficient'
                    END AS stock_status
             FROM supplies s
             JOIN categories c ON s.category_id = c.category_id
             WHERE {$w}
             ORDER BY shortage_quantity DESC",
            $params
        );
    }

    // ══════════════════════════════════════════════════════
    //  REPORT 4 – TOTAL PURCHASES PER SUPPLIER
    //  Admin only
    // ══════════════════════════════════════════════════════

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

    // ══════════════════════════════════════════════════════
    //  REPORT 5 – MOST CONSUMED SUPPLIES
    //  Admin + Staff
    // ══════════════════════════════════════════════════════

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