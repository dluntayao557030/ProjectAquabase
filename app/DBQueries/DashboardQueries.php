<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class DashboardQueries
{
    public static function getTotalSupplies(): int
    {
        return (int) DB::scalar("SELECT COUNT(*) FROM supplies WHERE status != 'inactive'");
    }

    public static function getSuppliesAddedToday(string $date): int
    {
        return (int) DB::scalar(
            "SELECT COUNT(*) 
             FROM stock_transactions 
             WHERE transaction_type = 'stock_in' 
               AND DATE(transaction_date) = ?",
            [$date]
        );
    }

    public static function getSuppliesUsedToday(string $date): int
    {
        return (int) DB::scalar(
            "SELECT COUNT(*) 
             FROM stock_transactions 
             WHERE transaction_type = 'stock_out' 
               AND DATE(transaction_date) = ?",
            [$date]
        );
    }

    public static function getCategoryData()
    {
        return DB::select(
            "SELECT c.category_name, 
                    SUM(s.current_stock) as total 
             FROM supplies s
             JOIN categories c ON s.category_id = c.category_id
             WHERE s.status != 'inactive'
             GROUP BY c.category_id, c.category_name
             ORDER BY total DESC"
        );
    }

    public static function getLowStockSupplies()
    {
        return DB::select(
            "SELECT s.supply_id as id,
                    s.supply_name,
                    c.category_name,
                    s.current_stock as stock,
                    s.reorder_level,
                    s.supply_img_path
             FROM supplies s
             JOIN categories c ON s.category_id = c.category_id
             WHERE s.current_stock <= s.reorder_level 
               AND s.status = 'active'
             ORDER BY (s.reorder_level - s.current_stock) DESC
             LIMIT 8"
        );
    }

    public static function getAllSuppliesForKpi()
    {
        return DB::select(
            "SELECT s.supply_name as name,
                    CONCAT(c.category_name, ' • ', s.current_stock, ' ', COALESCE(s.unit_measure, '')) as detail,
                    s.current_stock as value,
                    CASE 
                        WHEN s.current_stock = 0 THEN 'Out of Stock'
                        WHEN s.current_stock <= s.reorder_level THEN 'Low Stock'
                        ELSE 'In Stock'
                    END as status
             FROM supplies s
             JOIN categories c ON s.category_id = c.category_id
             WHERE s.status = 'active'
             ORDER BY s.current_stock ASC"
        );
    }

        public static function getStockInToday(string $date)
    {
        return DB::select(
            "SELECT s.supply_name as name,
                    CONCAT('Added ', st.quantity, ' ', COALESCE(s.unit_measure, '')) as detail,
                    st.quantity as value,
                    'stock_in' as status,
                    si.cost as unit_cost
            FROM stock_transactions st
            JOIN supplies s ON st.supply_id = s.supply_id
            LEFT JOIN stock_ins si ON st.transaction_id = si.transaction_id
            WHERE st.transaction_type = 'stock_in' 
            AND DATE(st.transaction_date) = ?
            ORDER BY st.transaction_date DESC",
            [$date]
        );
    }

    public static function getStockOutToday(string $date)
    {
        return DB::select(
            "SELECT s.supply_name as name,
                    CONCAT('Used ', st.quantity, ' ', COALESCE(s.unit_measure, '')) as detail,
                    st.quantity as value,
                    'stock_out' as status,
                    so.remarks
             FROM stock_transactions st
             JOIN supplies s ON st.supply_id = s.supply_id
             LEFT JOIN stock_outs so ON st.transaction_id = so.transaction_id
             WHERE st.transaction_type = 'stock_out' 
               AND DATE(st.transaction_date) = ?
             ORDER BY st.transaction_date DESC",
            [$date]
        );
    }
}