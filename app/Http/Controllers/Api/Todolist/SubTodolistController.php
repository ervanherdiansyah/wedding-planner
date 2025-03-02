<?php

namespace App\Http\Controllers\Api\Todolist;

use App\Http\Controllers\Controller;
use App\Models\CategoryTodolists;
use App\Models\Projects;
use App\Models\SubTodolists;
use App\Models\Todolists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubTodolistController extends Controller
{
    public function getSubTodolists()
    {
        try {
            $SubTodolists = SubTodolists::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $SubTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function statusCompleteSubTodolists($todolist_id)
    {
        try {
            $SubTodolists = SubTodolists::where('todolist_id', $todolist_id)->where('status', 1)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $SubTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function statusNotCompleteSubTodolists($todolist_id)
    {
        try {
            $SubTodolists = SubTodolists::where('todolist_id', $todolist_id)->where('status', 0)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $SubTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getSubTodolistsByTodolistsId($todolist_id)
    {
        try {
            $SubTodolists = SubTodolists::where('todolist_id', $todolist_id)->get();
            $completeStatusSubTodolists = SubTodolists::where('todolist_id', $todolist_id)->where('status', 1)->count();
            $notCompleteStatusSubTodolists = SubTodolists::where('todolist_id', $todolist_id)->where('status', 0)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => ['subTodolists' => $SubTodolists, 'completeStatus' => $completeStatusSubTodolists, 'notCompleteStatus' => $notCompleteStatusSubTodolists]], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getSubTodolistsById($id)
    {
        try {
            $SubTodolists = SubTodolists::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $SubTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createSubTodolists(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'todolist_id' => 'required',
            ]);

            $SubTodolists = SubTodolists::create([
                'todolist_id' => $request->todolist_id,
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $SubTodolists], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateSubTodolists(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $SubTodolists = SubTodolists::find($id);
            if (!$SubTodolists) {
                return response()->json(['message' => 'Sub Todolist not found'], 404);
            }
            // Update data bride
            $SubTodolists->update([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            // Update status Todolist jika semua subtodolist sudah selesai
            $todolist = Todolists::find($SubTodolists->todolist_id);
            if ($todolist) {
                // Cek apakah semua subtodolist di todolist sudah selesai
                $allCompleted = $todolist->subtodolist->every(function ($subTodolist) {
                    return $subTodolist->status === 1; // cek apakah semua subtodolist selesai
                });

                // Update status todolist berdasarkan status semua subtodolist
                $todolist->status = $allCompleted ? 1 : 0;
                $todolist->save();

                // Update status category_todolist jika semua todolist sudah selesai
                $categoryTodolist = CategoryTodolists::find($todolist->category_todolist_id);
                if ($categoryTodolist) {
                    $allCompleted = $categoryTodolist->todolist->every(function ($todolist) {
                        return $todolist->status === 1; // cek apakah semua todolist selesai
                    });

                    // Update status category_todolist berdasarkan status semua todolist
                    $categoryTodolist->status = $allCompleted ? 1 : 0;
                    $categoryTodolist->save();
                }
            }

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $SubTodolists], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteSubTodolists($id)
    {
        try {
            SubTodolists::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
