<?php

namespace App\Http\Middleware;

use App\DBQueries\AuthQueries;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        // 1. Check if user is logged in via session
        if (!Session::has('user_id')) {
            return redirect()->route('login');
        }

        $userId = Session::get('user_id');

        // 2. (Optional) Refresh user data from DB – ensures account still exists & active
        $user = AuthQueries::getUserById($userId);  // you need to add this method in AuthQueries

        if (!$user || $user->status !== 'active') {
            Session::flush();
            return redirect()->route('login')->with('error', 'Your account is no longer active.');
        }

        // 3. Session data might be stale – update first/last name if changed in DB
        Session::put('first_name', $user->first_name);
        Session::put('last_name', $user->last_name);
        Session::put('role', $user->role);

        // 4. Role‑based restriction (e.g. 'admin' or 'staff')
        if ($role && Session::get('role') !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}