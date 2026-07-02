<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $where    = ['1=1'];
        $bindings = [];

        if ($s = $request->search) {
            $where[]    = "v.vehicle_name LIKE ?";
            $bindings[] = "%{$s}%";
        }
        if ($status = $request->status) {
            $where[]    = "m.status = ?";
            $bindings[] = $status;
        }
        $whereSql = implode(' AND ', $where);

        $total = DB::select("
            SELECT COUNT(*) AS total FROM maintenance_records m
            INNER JOIN vehicles v ON v.id = m.vehicle_id
            WHERE {$whereSql}
        ", $bindings)[0]->total;

        $rows = DB::select("
            SELECT m.*, v.vehicle_name
            FROM maintenance_records m
            INNER JOIN vehicles v ON v.id = m.vehicle_id
            WHERE {$whereSql}
            ORDER BY m.maintenance_date DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $bindings);

        $records = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalCost = DB::select("SELECT COALESCE(SUM(cost),0) AS total FROM maintenance_records")[0]->total;

        return view('admin.maintenance.index', compact('records', 'totalCost'));
    }

    public function create()
    {
        $vehicles = DB::select("SELECT * FROM vehicles ORDER BY vehicle_name ASC");
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

        DB::insert("
            INSERT INTO maintenance_records (
                vehicle_id, maintenance_date, next_maintenance_date, maintenance_type,
                description, cost, status, mechanic_name, service_center, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $data['vehicle_id'], $data['maintenance_date'], $data['next_maintenance_date'] ?? null,
            $data['maintenance_type'], $data['description'], $data['cost'], $data['status'],
            $data['mechanic_name'] ?? null, $data['service_center'] ?? null,
        ]);

        if ($data['status'] === 'in_progress') {
            DB::update("UPDATE vehicles SET status = 'maintenance', updated_at = NOW() WHERE id = ?", [$data['vehicle_id']]);
        }

        return redirect()->route('admin.maintenance.index')->with('success', 'Maintenance record added.');
    }

    public function edit(int $id)
    {
        $maintenance = DB::select("SELECT * FROM maintenance_records WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($maintenance), 404);

        $vehicles = DB::select("SELECT * FROM vehicles ORDER BY vehicle_name ASC");

        return view('admin.maintenance.edit', ['maintenance' => $maintenance[0], 'vehicles' => $vehicles]);
    }

    public function update(Request $request, int $id)
    {
        abort_if(empty(DB::select("SELECT id FROM maintenance_records WHERE id = ? LIMIT 1", [$id])), 404);

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

        DB::update("
            UPDATE maintenance_records SET
                vehicle_id = ?, maintenance_date = ?, next_maintenance_date = ?, maintenance_type = ?,
                description = ?, cost = ?, status = ?, mechanic_name = ?, service_center = ?, updated_at = NOW()
            WHERE id = ?
        ", [
            $data['vehicle_id'], $data['maintenance_date'], $data['next_maintenance_date'] ?? null,
            $data['maintenance_type'], $data['description'], $data['cost'], $data['status'],
            $data['mechanic_name'] ?? null, $data['service_center'] ?? null, $id,
        ]);

        if ($data['status'] === 'completed') {
            DB::update("UPDATE vehicles SET status = 'available', updated_at = NOW() WHERE id = ?", [$data['vehicle_id']]);
        } elseif ($data['status'] === 'in_progress') {
            DB::update("UPDATE vehicles SET status = 'maintenance', updated_at = NOW() WHERE id = ?", [$data['vehicle_id']]);
        }

        return redirect()->route('admin.maintenance.index')->with('success', 'Record updated.');
    }

    public function destroy(int $id)
    {
        DB::delete("DELETE FROM maintenance_records WHERE id = ?", [$id]);
        return redirect()->route('admin.maintenance.index')->with('success', 'Record deleted.');
    }
}
