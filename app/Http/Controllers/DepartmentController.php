<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('departments.index');
    }

    public function getData(Request $request)
    {
        $departments = Department::withCount('employees')->get();
        return response()->json($departments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:departments,code|max:10',
            'name' => 'required|string|max:255',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil ditambahkan',
            'data' => $department
        ]);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'code' => 'required|unique:departments,code,' . $department->id . '|max:10',
            'name' => 'required|string|max:255',
        ]);

        $department->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data departemen berhasil diupdate',
            'data' => $department
        ]);
    }

    public function destroy(Department $department)
    {
        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus departemen yang masih memiliki karyawan'
            ], 400);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil dihapus'
        ]);
    }
}
