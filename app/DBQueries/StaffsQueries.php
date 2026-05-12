<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class StaffsQueries
{
    /**
     * Get all staff users (role = 'staff').
     */
    public static function getAllStaff()
    {
        return DB::select("
            SELECT 
                user_id,
                first_name,
                last_name,
                email,
                username,
                role,
                status,
                CONCAT(first_name, ' ', last_name) as full_name,
                created_at,
                updated_at
            FROM users
            WHERE role = 'staff'
            ORDER BY first_name ASC
        ");
    }

    /**
     * Get a single staff member by user_id.
     */
    public static function getStaffById(int $userId)
    {
        return DB::selectOne("
            SELECT 
                user_id,
                first_name,
                last_name,
                email,
                username,
                role,
                status,
                CONCAT(first_name, ' ', last_name) as full_name,
                created_at,
                updated_at
            FROM users
            WHERE user_id = ? AND role = 'staff'
        ", [$userId]);
    }

    /**
     * Check if a username already exists (excluding a given user_id for updates).
     */
    public static function usernameExists(string $username, ?int $excludeUserId = null): bool
    {
        $query = "SELECT COUNT(*) as count FROM users WHERE username = ? AND role = 'staff'";
        $params = [$username];
        if ($excludeUserId) {
            $query .= " AND user_id != ?";
            $params[] = $excludeUserId;
        }
        $result = DB::selectOne($query, $params);
        return $result->count > 0;
    }

    /**
     * Check if an email already exists (excluding a given user_id).
     */
    public static function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = "SELECT COUNT(*) as count FROM users WHERE email = ? AND role = 'staff'";
        $params = [$email];
        if ($excludeUserId) {
            $query .= " AND user_id != ?";
            $params[] = $excludeUserId;
        }
        $result = DB::selectOne($query, $params);
        return $result->count > 0;
    }
}