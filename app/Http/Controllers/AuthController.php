<?php

namespace App\Http\Controllers;

use App\DBQueries\AuthQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Session::has('user_id')) {
            return $this->redirectByRole(Session::get('role'));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = AuthQueries::getUserByUsername($request->username);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Invalid username or password.');
        }

        if ($user->status !== 'active') {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        Session::put('user_id',    $user->user_id);
        Session::put('username',   $user->username);
        Session::put('first_name', $user->first_name);
        Session::put('last_name',  $user->last_name);
        Session::put('role',       $user->role);

        if ($request->boolean('remember')) {
            config(['session.lifetime' => 60 * 24 * 30]);
        }

        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(string $role)
{
    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'staff' => redirect()->route('staff.transactions.index'), // matches staff prefix
        default => redirect()->route('login'),
    };
}
}