<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\CategoryVendors;
use Illuminate\Http\Request;

class CategoryVendorController extends Controller
{
    public function getCategoryVendors()
    {
        try {
            $CategoryVendors = CategoryVendors::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryVendors], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryVendorsByProjectId($project_id)
    {
        try {
            $CategoryVendors = CategoryVendors::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryVendors], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryVendorsById($id)
    {
        try {
            $CategoryVendors = CategoryVendors::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryVendors], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createCategoryVendors(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $CategoryVendors = CategoryVendors::create([
                'project_id' => $request->project_id,
                'name' => $request->name,
                'icon' => $request->icon,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $CategoryVendors], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateCategoryVendors(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'icon' => 'required|string|max:255',
            ]);

            // Cari data bride berdasarkan ID
            $CategoryVendors = CategoryVendors::find($id);
            if (!$CategoryVendors) {
                return response()->json(['message' => 'Category Vendor not found'], 404);
            }
            // Update data bride
            $CategoryVendors->update([
                'name' => $request->name,
                'icon' => $request->icon,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $CategoryVendors], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteCategoryVendors($id)
    {
        try {
            CategoryVendors::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
