<?php

namespace App\Http\Controllers\Api\Todolist;

use App\Http\Controllers\Controller;
use App\Models\CategoryTodolists;
use App\Models\Projects;
use App\Models\Todolists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodolistController extends Controller
{
    public function getTodolists()
    {
        try {
            $Todolists = Todolists::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Todolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function allStatusCompleteTodolists()
    {
        try {
            $Todolists = Todolists::where('status', 1)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Todolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function allStatusNotCompleteTodolists()
    {
        try {
            $Todolists = Todolists::where('status', 0)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Todolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function statusCompleteTodolists($category_todolist_id)
    {
        try {
            $Todolists = Todolists::where('category_todolist_id', $category_todolist_id)->where('status', 1)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Todolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function statusNotCompleteTodolists($category_todolist_id)
    {
        try {
            $Todolists = Todolists::where('category_todolist_id', $category_todolist_id)->where('status', 0)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Todolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getTodolistsByCategoryTodolistsId($category_todolist_id)
    {
        try {
            $Todolists = Todolists::where('category_todolist_id', $category_todolist_id)->get();
            $completeStatusTodolists = Todolists::where('category_todolist_id', $category_todolist_id)->where('status', 1)->count();
            $notCompleteStatusTodolists = Todolists::where('category_todolist_id', $category_todolist_id)->where('status', 0)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => ['todolists' => $Todolists, 'completeStatus' => $completeStatusTodolists, 'notCompleteStatus' => $notCompleteStatusTodolists]], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getTodolistsByAllCategoryTodolistsId()
    {
        try {
            $user = Auth::user();
            $project = Projects::where('user_id', $user->id)->first();
            if (!$project) {
                return response()->json(['message' => 'Project not found for this user'], 404);
            }
            $CategoryTodolists = CategoryTodolists::where('project_id', $project->id)->get();

            $Todolists = Todolists::where('project_id', $project->id)
                ->with('todolist')
                ->get()
                ->map(function ($category) {
                    return [
                        'project_id' => $category->project_id,
                        'category_id' => $category->id,
                        'category_name' => $category->name,
                        'status' => $category->status,
                        'todolists' => $category->todolist
                    ];
                });
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Todolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getTodolistsById($id)
    {
        try {
            $Todolists = Todolists::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Todolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createTodolists(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'category_todolist_id' => 'required',
            ]);

            $Todolists = Todolists::create([
                'category_todolist_id' => $request->category_todolist_id,
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Todolists], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateTodolists(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $Todolists = Todolists::find($id);
            if (!$Todolists) {
                return response()->json(['message' => 'Todolist not found'], 404);
            }
            // Update data bride
            $Todolists->update([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Todolists], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteTodolists($id)
    {
        try {
            Todolists::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
