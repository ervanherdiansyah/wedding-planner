<?php

namespace App\Http\Controllers\Api\Uniform;

use App\Http\Controllers\Controller;
use App\Models\Uniform;
use Illuminate\Http\Request;

class UniformController extends Controller
{
    public function getUniform()
    {
        try {
            $Uniform = Uniform::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Uniform], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getUniformByProjectId($project_id)
    {
        try {
            $Uniform = Uniform::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Uniform], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getUniformById($id)
    {
        try {
            $Uniform = Uniform::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Uniform], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createUniform(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'uniform_category_id' => 'required',
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'attire' => 'required|string|max:255',
                'note' => 'required|string',
            ]);

            $Uniform = Uniform::create([
                'uniform_category_id' => $request->uniform_category_id,
                'name' => $request->name,
                'status' => $request->status,
                'attire' => $request->attire,
                'note' => $request->note,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Uniform], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateUniform(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'attire' => 'required|string|max:255',
                'note' => 'required|string',
            ]);

            // Cari data bride berdasarkan ID
            $Uniform = Uniform::find($id);
            if (!$Uniform) {
                return response()->json(['message' => 'Uniform Category not found'], 404);
            }
            // Update data bride
            $Uniform->update([
                'name' => $request->name,
                'status' => $request->status,
                'attire' => $request->attire,
                'note' => $request->note,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Uniform], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteUniform($id)
    {
        try {
            Uniform::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
