<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class InventoryQueries
{
    public static function getAllSupplies()
    {
        return DB::select("
            SELECT s.*, c.category_name 
            FROM supplies s
            JOIN categories c ON s.category_id = c.category_id
            ORDER BY s.supply_name ASC
        ");
    }

    public static function getSupplyById(int $id)
    {
        return DB::selectOne("
            SELECT s.*, c.category_name 
            FROM supplies s
            JOIN categories c ON s.category_id = c.category_id
            WHERE s.supply_id = ?
        ", [$id]);
    }

    public static function getCategories()
    {
        return DB::select("SELECT * FROM categories ORDER BY category_name");
    }
}