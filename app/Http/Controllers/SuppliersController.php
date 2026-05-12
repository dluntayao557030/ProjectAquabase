<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuppliersController extends Controller
{
    public function index()
    {
        $suppliers = DB::table('suppliers')
            ->select(
                'supplier_id',
                'supplier_name',
                'contact_no',
                'email',
                'address',
                'description',
                'status as supplier_status'
            )
            ->orderBy('supplier_name')
            ->get();

        $suppliersData = $suppliers->map(function ($s) {
            return [
                'id'              => $s->supplier_id,
                'supplier_name'   => $s->supplier_name,
                'description'     => $s->description,
                'contact_number'  => $s->contact_no,
                'email'           => $s->email,
                'address'         => $s->address,
                'supplier_status' => $s->supplier_status ?? 'active',
                'display_id'      => 'SUP' . str_pad($s->supplier_id, 3, '0', STR_PAD_LEFT),
                'edit_url'        => route('admin.suppliers.update', $s->supplier_id),
                'delete_url'      => route('admin.suppliers.destroy', $s->supplier_id),
            ];
        });

        return view('admin_suppliers.index', compact('suppliersData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:150',
            'contact_no'    => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:150',
            'address'       => 'nullable|string|max:255',
            'description'   => 'nullable|string',
        ]);

        DB::table('suppliers')->insert([
            'supplier_name' => $request->supplier_name,
            'contact_no'    => $request->contact_no,
            'email'         => $request->email,
            'address'       => $request->address,
            'description'   => $request->description,
            'status'        => 'active',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('admin.suppliers.index')
                         ->with('success', 'Supplier added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:150',
            'contact_no'    => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:150',
            'address'       => 'nullable|string|max:255',
            'description'   => 'nullable|string',
        ]);

        DB::table('suppliers')->where('supplier_id', $id)->update([
            'supplier_name' => $request->supplier_name,
            'contact_no'    => $request->contact_no,
            'email'         => $request->email,
            'address'       => $request->address,
            'description'   => $request->description,
            'updated_at'    => now(),
        ]);

        return redirect()->route('admin.suppliers.index')
                         ->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        $supplier = DB::table('suppliers')->where('supplier_id', $id)->first();

        if (!$supplier) {
            return redirect()->route('admin.suppliers.index')
                            ->with('error', 'Supplier not found.');
        }

        $newStatus = $supplier->status === 'active' ? 'inactive' : 'active';

        DB::table('suppliers')->where('supplier_id', $id)->update([
            'status'     => $newStatus,
            'updated_at' => now(),
        ]);

        $message = $newStatus === 'active' 
            ? 'Supplier reactivated successfully.' 
            : 'Supplier deactivated successfully.';

        return redirect()->route('admin.suppliers.index')
                        ->with('success', $message);
    }
}