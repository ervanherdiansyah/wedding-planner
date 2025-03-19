<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brides;
use App\Models\CategoryTodolists;
use App\Models\Events;
use App\Models\Grooms;
use App\Models\SubTodolists;
use App\Models\Todolists;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function getOverview($project_id)
    {
        try {
            // Information Bride and Groom
            $bride = Brides::where('project_id', $project_id)->get();
            $groom = Grooms::where('project_id', $project_id)->first();

            // Event
            $events = Events::where('project_id', $project_id)->get();

            // Hitung status completed
            $completedSubTodolists = SubTodolists::whereIn('todolist_id', Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project_id)->pluck('id'))->pluck('id'))->where('status', 1)->count();
            $totalCompleted = $completedSubTodolists;

            // Hitung status not completed
            $notCompletedSubTodolists = SubTodolists::whereIn('todolist_id', Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project_id)->pluck('id'))->pluck('id'))->where('status', 0)->count();
            $totalNotCompleted = $notCompletedSubTodolists;

            // Hitung total todolist
            $totalTask = $totalCompleted + $totalNotCompleted;

            return response()->json(['message' => 'Fetch Data Successfully', 'information_bridegroom' => ['bride' => $bride, 'groom' => $groom], "events" => $events, 'progress_list' => ['totalTask' => $totalTask, 'totalCompleted' => $totalCompleted, 'totalNotCompleted' => $totalNotCompleted]], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
