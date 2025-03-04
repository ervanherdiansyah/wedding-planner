<?php

namespace App\Http\Controllers\Api\Uniform;

use App\Http\Controllers\Controller;
use App\Models\Projects;
use App\Models\Uniform;
use App\Models\UniformCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function getUniformByUniformCategoryId($project_id)
    {
        try {
            // $user = Auth::user();
            // $project = Projects::where('user_id', $user->id)->first();
            $uniform = UniformCategories::where('project_id', $project_id)
                ->with(['uniform']) // Nested eager loading
                ->get()
                ->map(function ($category) {
                    $delivered_items = $category->uniform->where('status', 'Sudah Diberikan')->count();
                    return [
                        'id' => $category->id,
                        'project_id' => $category->project_id,
                        'category_name' => $category->title,
                        "total_uniform" => $category->uniform->count(),
                        "delivered_items" => $delivered_items,
                        'uniform' => $category->uniform->map(function ($uniform) {
                            return [
                                'id' => $uniform->id,
                                'category_id' => $uniform->uniform_category_id,
                                'uniform_name' => $uniform->name,
                                'uniform_status' => $uniform->status,
                                'uniform_attire' => $uniform->attire,
                                'uniform_note' => $uniform->note,
                            ];
                        }),
                    ];
                });
            $total_category = UniformCategories::where('project_id', $project_id)->count();

            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $uniform, 'total_category' => $total_category], 200);
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
            Request()->validate([]);

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
            $request->validate([]);

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
