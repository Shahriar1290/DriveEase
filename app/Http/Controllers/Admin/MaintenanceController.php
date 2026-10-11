<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with('vehicle:id,vehicle_name');

        if ($s = $request->search) {
            $query->whereHas('vehicle', fn($q) => $q->where('vehicle_name', 'like', "%{$s}%"));
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $records = $query->orderByDesc('maintenance_date')->paginate(15)->withQueryString();
        $totalCost = MaintenanceRecord::sum('cost');

        return view('admin.maintenance.index', compact('records', 'totalCost'));
    }

    public function create()
    {
        $vehicles = Vehicle::orderBy('vehicle_name')->get();
        return view('admin.maintenance.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id'             => 'required|integer',
            'maintenance_date'       => 'required|date',
            'next_maintenance_date'  => 'nullable|date|after:maintenance_date',
            'maintenance_type'       => 'required|string|max:100',
            'description'            => 'required|string',
            'cost'                   => 'required|numeric|min:0',
            'status'                 => 'required|in:scheduled,in_progress,completed',
            'mechanic_name'          => 'nullable|string|max:100',
            'service_center'         => 'nullable|string|max:255',
        ]);

        MaintenanceRecord::create($data);

        if ($data['status'] === 'in_progress') {
            Vehicle::where('id', $data['vehicle_id'])->update(['status' => 'maintenance']);
        }

        return redirect()->route('admin.maintenance.index')->with('success', 'Maintenance record added.');
    }

    public function edit(int $id)
    {
        $maintenance = MaintenanceRecord::findOrFail($id);
        $vehicles = Vehicle::orderBy('vehicle_name')->get();

        return view('admin.maintenance.create', compact('maintenance', 'vehicles'));
    }

    public function update(Request $request, int $id)
    {
        $maintenance = MaintenanceRecord::findOrFail($id);

        $data = $request->validate([
            'vehicle_id'             => 'required|integer',
            'maintenance_date'       => 'required|date',
            'next_maintenance_date'  => 'nullable|date',
            'maintenance_type'       => 'required|string|max:100',
            'description'            => 'required|string',
            'cost'                   => 'required|numeric|min:0',
            'status'                 => 'required|in:scheduled,in_progress,completed',
            'mechanic_name'          => 'nullable|string|max:100',
            'service_center'         => 'nullable|string|max:255',
        ]);

        $maintenance->update($data);

        if ($data['status'] === 'completed') {
            Vehicle::where('id', $data['vehicle_id'])->update(['status' => 'available']);
        } elseif ($data['status'] === 'in_progress') {
            Vehicle::where('id', $data['vehicle_id'])->update(['status' => 'maintenance']);
        }

        return redirect()->route('admin.maintenance.index')->with('success', 'Record updated.');
    }

    public function destroy(int $id)
    {
        MaintenanceRecord::findOrFail($id)->delete();
        return redirect()->route('admin.maintenance.index')->with('success', 'Record deleted.');
    }
}
