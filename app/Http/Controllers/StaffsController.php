<?php

namespace App\Http\Controllers;

use App\DBQueries\StaffsQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffsController extends Controller
{
    public function index()
    {
        $staffs = StaffsQueries::getAllStaff();

        $staffsData = collect($staffs)->map(function ($s) {
            return [
                'id'           => $s->user_id,
                'user_id'      => $s->user_id,
                'full_name'    => $s->full_name,
                'first_name'   => $s->first_name,
                'last_name'    => $s->last_name,
                'email'        => $s->email,
                'username'     => $s->username,
                'staff_status' => $s->status,   // 'active' or 'inactive'
                'display_id'   => 'STF' . str_pad($s->user_id, 3, '0', STR_PAD_LEFT),
                'update_url'   => route('admin.staffs.update', $s->user_id),
                'delete_url'   => route('admin.staffs.destroy', $s->user_id),
            ];
        });

        return view('admin_staffs.index', compact('staffs', 'staffsData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'username'   => 'required|string|unique:users,username',
            'password'   => 'required|min:8|confirmed',
        ]);

        $userId = DB::table('users')->insertGetId([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'role'       => 'staff',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.staffs.index')
                         ->with('success', 'Staff added successfully!');
    }

    public function update(Request $request, $id)
    {
        // $id is user_id
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'username'   => 'required|string',
            'status'     => 'required|in:active,inactive',
            'password'   => 'nullable|min:8|confirmed',
        ]);

        // Check username uniqueness (excluding current user)
        if (StaffsQueries::usernameExists($request->username, $id)) {
            return back()->withErrors(['username' => 'Username already taken.'])->withInput();
        }

        $updateData = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'username'   => $request->username,
            'status'     => $request->status,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('user_id', $id)->where('role', 'staff')->update($updateData);

        return redirect()->route('admin.staffs.index')
                         ->with('success', 'Staff updated successfully!');
    }

    public function destroy($id)
    {
        $user = DB::table('users')->where('user_id', $id)->where('role', 'staff')->first();
        if (!$user) return redirect()->back()->with('error', 'Staff not found');

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        DB::table('users')->where('user_id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        $message = $newStatus === 'active' ? 'Staff reactivated successfully.' : 'Staff deactivated successfully.';
        return redirect()->route('admin.staffs.index')->with('success', $message);
    }
}