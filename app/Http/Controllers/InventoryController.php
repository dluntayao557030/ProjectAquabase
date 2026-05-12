<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DBQueries\InventoryQueries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index()
    {
        $supplies = InventoryQueries::getAllSupplies();
        $categories = InventoryQueries::getCategories();

        $suppliesData = collect($supplies)->map(function ($s) {
            return [
                'id'              => $s->supply_id,
                'supply_name'     => $s->supply_name,
                'category_id'     => $s->category_id,
                'category_name'   => $s->category_name,
                'stock'           => $s->current_stock,
                'reorder_level'   => $s->reorder_level,
                'supply_status'   => $s->status ?? 'active',
                'supply_img_path' => $s->supply_img_path,
                'img_url'         => $s->supply_img_path ? asset('storage/' . $s->supply_img_path) : null,
                'display_id'      => strtoupper(substr($s->category_name ?? 'SUP', 0, 3)) . str_pad($s->supply_id, 4, '0', STR_PAD_LEFT),
                'edit_url'        => route('admin.inventory.update', $s->supply_id),
                'toggle_url'      => route('admin.inventory.destroy', $s->supply_id), // reuse destroy route
            ];
        });

        return view('admin_inventory.index', compact('supplies', 'suppliesData', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supply_name'   => 'required|string|max:200',
            'category_id'   => 'required|exists:categories,category_id',
            'reorder_level' => 'required|integer|min:0',
            'supply_image'  => 'nullable|image|max:2048',
            'unit_measure' => 'required|string|max:50',
        ]);

        $imagePath = null;
        if ($request->hasFile('supply_image')) {
            $imagePath = $request->file('supply_image')->store('supplies', 'public');
        }

        DB::table('supplies')->insert([
            'category_id'     => $request->category_id,
            'supply_name'     => $request->supply_name,
            'supply_img_path' => $imagePath,
            'reorder_level'   => $request->reorder_level,
            'current_stock'   => 0,
            'unit_measure'   => $request->unit_measure,
            'status'          => 'active',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()->route('admin.inventory.index')
                         ->with('success', 'New supply added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supply_name'   => 'required|string|max:200',
            'category_id'   => 'required|exists:categories,category_id',
            'reorder_level' => 'required|integer|min:0',
            'unit_measure'   => 'required|string|max:50',
            'supply_image'  => 'nullable|image|max:2048',
        ]);

        $data = [
            'supply_name'   => $request->supply_name,
            'category_id'   => $request->category_id,
            'unit_measure'  => $request->unit_measure,
            'reorder_level' => $request->reorder_level,
            'updated_at'    => now(),
        ];

        if ($request->hasFile('supply_image')) {
            $imagePath = $request->file('supply_image')->store('supplies', 'public');
            $data['supply_img_path'] = $imagePath;
        }

        DB::table('supplies')->where('supply_id', $id)->update($data);

        return redirect()->route('admin.inventory.index')
                         ->with('success', 'Supply updated successfully!');
    }

    /**
     * Toggle supply status (active ↔ inactive) – used by the deactivate/reactivate button.
     */
    public function destroy($id)
    {
        $supply = DB::table('supplies')->where('supply_id', $id)->first();
        if (!$supply) {
            return redirect()->route('admin.inventory.index')
                             ->with('error', 'Supply not found.');
        }

        $newStatus = $supply->status === 'active' ? 'inactive' : 'active';
        DB::table('supplies')->where('supply_id', $id)->update([
            'status'     => $newStatus,
            'updated_at' => now(),
        ]);

        $message = $newStatus === 'active' ? 'Supply reactivated successfully.' : 'Supply deactivated successfully.';
        return redirect()->route('admin.inventory.index')->with('success', $message);
    }
}