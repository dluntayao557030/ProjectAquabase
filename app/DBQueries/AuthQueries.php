<?php

namespace App\DBQueries;

use Illuminate\Support\Facades\DB;

class AuthQueries
{
    public static function getUserByUsername(string $username): ?object
    {
        return DB::selectOne(
            'SELECT user_id, first_name, last_name, email, username, password, role, status
             FROM users
             WHERE username = ?
             LIMIT 1',
            [$username]
        );
    }

    public static function getUserById(int $userId): ?object
    {
        return DB::selectOne(
            'SELECT user_id, first_name, last_name, email, username, role, status
             FROM users
             WHERE user_id = ?
             LIMIT 1',
            [$userId]
        );
    }
}