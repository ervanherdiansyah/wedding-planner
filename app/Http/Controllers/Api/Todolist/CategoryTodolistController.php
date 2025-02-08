<?php

namespace App\Http\Controllers\Api\Todolist;

use App\Http\Controllers\Controller;
use App\Models\CategoryTodolists;
use App\Models\Projects;
use App\Models\SubTodolists;
use App\Models\Todolists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryTodolistController extends Controller
{
    public function getCategoryTodolists()
    {
        try {
            $CategoryTodolists = CategoryTodolists::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryTodolistsByAllProject()
    {
        try {
            $user = Auth::user();
            $project = Projects::where('user_id', $user->id)->first();
            $CategoryTodolists = CategoryTodolists::where('project_id', $project->id)
                ->with(['todolist.subtodolists']) // Nested eager loading
                ->get()
                ->map(function ($category) {
                    return [
                        'category_id' => $category->id,
                        'category_name' => $category->name,
                        'todolists' => $category->todolist->map(function ($todolist) {
                            return [
                                'todolist_id' => $todolist->id,
                                'todolist_name' => $todolist->name,
                                'subtodolists' => $todolist->subtodolists->map(function ($subtodolist) {
                                    return [
                                        'subtodolist_id' => $subtodolist->id,
                                        'subtodolist_name' => $subtodolist->name,
                                        'status' => $subtodolist->status,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function statusCompleteCategoryTodolists()
    {
        try {
            $user = Auth::user();
            $project = Projects::where('user_id', $user->id)->first();
            $CategoryTodolists = CategoryTodolists::where('project_id', $project->id)->where('status', 1)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function statusNotCompleteCategoryTodolists()
    {
        try {
            $user = Auth::user();
            $project = Projects::where('user_id', $user->id)->first();
            $CategoryTodolists = CategoryTodolists::where('project_id', $project->id)->where('status', 0)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryTodolistsByProjectId($project_id)
    {
        try {
            $CategoryTodolists = CategoryTodolists::where('project_id', $project_id)->get();
            $completeStatusCategoryTodolists = CategoryTodolists::where('project_id', $project_id)->where('status', 1)->count();
            $notCompleteStatusCategoryTodolists = CategoryTodolists::where('project_id', $project_id)->where('status', 0)->count();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => ['categoryTodolists' => $CategoryTodolists, 'completeStatus' => $completeStatusCategoryTodolists, 'notCompleteStatus' => $notCompleteStatusCategoryTodolists]], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryTodolistsById($id)
    {
        try {
            $CategoryTodolists = CategoryTodolists::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryTodolists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createCategoryTodolists(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $CategoryTodolists = CategoryTodolists::create([
                'project_id' => $request->project_id,
                'name' => $request->name,
                'status' => $request->status,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $CategoryTodolists], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateCategoryTodolists(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $CategoryTodolists = CategoryTodolists::find($id);
            if (!$CategoryTodolists) {
                return response()->json(['message' => 'Category Todolist not found'], 404);
            }
            // Update data bride
            $CategoryTodolists->update([
                'name' => $request->name,
                'status' => $request->status,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $CategoryTodolists], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteCategoryTodolists($id)
    {
        try {
            CategoryTodolists::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function getAllTodolistsByProject()
    {
        try {
            $user = Auth::user();
            $project = Projects::where('user_id', $user->id)->first();

            if (!$project) {
                return response()->json(['message' => 'Project not found for this user'], 404);
            }

            // Ambil semua data todolist beserta subtodolist
            $todolist = CategoryTodolists::where('project_id', $project->id)
                ->with(['todolist.subtodolist']) // Nested eager loading
                ->get()
                ->map(function ($category) {
                    return [
                        'category_id' => $category->id,
                        'project_id' => $category->project_id,
                        'name' => $category->name,
                        'status' => (bool) $category->status,
                        'todolists' => $category->todolist->map(function ($todolist) {
                            return [
                                'todolist_id' => $todolist->id,
                                'todolist_name' => $todolist->name,
                                'status' => (bool) $todolist->status,
                                'subtodolists' => $todolist->subtodolist->map(function ($subtodolist) {
                                    return [
                                        'subtodolist_id' => $subtodolist->id,
                                        'subtodolist_name' => $subtodolist->name,
                                        'status' => (bool) $subtodolist->status,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });

            // Hitung status completed
            $completedCategories = CategoryTodolists::where('project_id', $project->id)->where('status', 1)->count();
            $completedTodolists = Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project->id)->pluck('id'))->where('status', 1)->count();
            $completedSubTodolists = SubTodolists::whereIn('todolist_id', Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project->id)->pluck('id'))->pluck('id'))->where('status', 1)->count();
            $totalCompleted = $completedCategories + $completedTodolists + $completedSubTodolists;

            // Hitung status not completed
            $notCompletedCategories = CategoryTodolists::where('project_id', $project->id)->where('status', 0)->count();
            $notCompletedTodolists = Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project->id)->pluck('id'))->where('status', 0)->count();
            $notCompletedSubTodolists = SubTodolists::whereIn('todolist_id', Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project->id)->pluck('id'))->pluck('id'))->where('status', 0)->count();
            $totalNotCompleted = $notCompletedCategories + $notCompletedTodolists + $notCompletedSubTodolists;

            // Return JSON response
            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $todolist,
                'statusCompleted' => [
                    'completed_categories' => $completedCategories,
                    'completed_todolists' => $completedTodolists,
                    'completed_subtodolists' => $completedSubTodolists,
                    'total_completed' => $totalCompleted,
                ],
                'statusNotCompleted' => [
                    'not_completed_categories' => $notCompletedCategories,
                    'not_completed_todolists' => $notCompletedTodolists,
                    'not_completed_subtodolists' => $notCompletedSubTodolists,
                    'total_not_completed' => $totalNotCompleted,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }



    public function countCompletedStatuses()
    {
        try {
            $user = Auth::user();
            $project = Projects::where('user_id', $user->id)->first();

            if (!$project) {
                return response()->json(['message' => 'Project not found for this user'], 404);
            }

            // Hitung status `1` di CategoryTodolists
            $completedCategories = CategoryTodolists::where('project_id', $project->id)
                ->where('status', 1)
                ->count();

            // Hitung status `1` di Todolists
            $completedTodolists = Todolists::whereIn(
                'category_todolist_id',
                CategoryTodolists::where('project_id', $project->id)->pluck('id')
            )->where('status', 1)->count();

            // Hitung status `1` di SubTodolists
            $completedSubTodolists = SubTodolists::whereIn(
                'todolist_id',
                Todolists::whereIn(
                    'category_todolist_id',
                    CategoryTodolists::where('project_id', $project->id)->pluck('id')
                )->pluck('id')
            )->where('status', 1)->count();

            // Total semua
            $totalCompleted = $completedCategories + $completedTodolists + $completedSubTodolists;

            return response()->json([
                'message' => 'Count Data Successfully',
                'data' => [
                    'completed_categories' => $completedCategories,
                    'completed_todolists' => $completedTodolists,
                    'completed_subtodolists' => $completedSubTodolists,
                    'total_completed' => $totalCompleted,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function countNotCompletedStatuses()
    {
        try {
            $user = Auth::user();
            $project = Projects::where('user_id', $user->id)->first();

            if (!$project) {
                return response()->json(['message' => 'Project not found for this user'], 404);
            }

            // Hitung status `0` di CategoryTodolists
            $notcompletedCategories = CategoryTodolists::where('project_id', $project->id)
                ->where('status', 0)
                ->count();

            // Hitung status `0` di Todolists
            $notcompletedTodolists = Todolists::whereIn(
                'category_todolist_id',
                CategoryTodolists::where('project_id', $project->id)->pluck('id')
            )->where('status', 0)->count();

            // Hitung status `0` di SubTodolists
            $notcompletedSubTodolists = SubTodolists::whereIn(
                'todolist_id',
                Todolists::whereIn(
                    'category_todolist_id',
                    CategoryTodolists::where('project_id', $project->id)->pluck('id')
                )->pluck('id')
            )->where('status', 0)->count();

            // Total semua
            $totalnotCompleted = $notcompletedCategories + $notcompletedTodolists + $notcompletedSubTodolists;

            return response()->json([
                'message' => 'Count Data Successfully',
                'data' => [
                    'not_completed_categories' => $notcompletedCategories,
                    'not_completed_todolists' => $notcompletedTodolists,
                    'not_completed_subtodolists' => $notcompletedSubTodolists,
                    'total_not_completed' => $totalnotCompleted,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
