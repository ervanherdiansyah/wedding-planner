<?php

namespace App\Http\Controllers\Api\Uniform;

use App\Http\Controllers\Controller;
use App\Models\UniformCategories;
use Illuminate\Http\Request;

class UniformCategoryController extends Controller
{
    public function getUniformCategories()
    {
        try {
            $UniformCategories = UniformCategories::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $UniformCategories], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getUniformCategoriesByProjectId($project_id)
    {
        try {
            $UniformCategories = UniformCategories::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $UniformCategories], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getUniformCategoriesById($id)
    {
        try {
            $UniformCategories = UniformCategories::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $UniformCategories], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createUniformCategories(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $UniformCategories = UniformCategories::create([
                'project_id' => $request->project_id,
                'title' => $request->title,
                'description' => $request->description,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $UniformCategories], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateUniformCategories(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $UniformCategories = UniformCategories::find($id);
            if (!$UniformCategories) {
                return response()->json(['message' => 'Uniform Category not found'], 404);
            }
            // Update data bride
            $UniformCategories->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $UniformCategories], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteUniformCategories($id)
    {
        try {
            UniformCategories::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
