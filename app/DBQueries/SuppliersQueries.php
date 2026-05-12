<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class SuppliersQueries
{
    public static function getAllSuppliers()
    {
        return DB::select("
            SELECT 
                supplier_id,
                supplier_name,
                description,
                contact_no,
                email,
                address,
                status,
                created_at,
                updated_at
            FROM suppliers
            ORDER BY supplier_name ASC
        ");
    }

    public static function getSupplierById(int $id)
    {
        return DB::selectOne("
            SELECT 
                supplier_id,
                supplier_name,
                description,
                contact_no,
                email,
                address,
                status,
                created_at,
                updated_at
            FROM suppliers
            WHERE supplier_id = ?
        ", [$id]);
    }

    // No categories needed for suppliers
}