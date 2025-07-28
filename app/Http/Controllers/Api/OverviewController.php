<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brides;
use App\Models\Budgets;
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
            $bride = Brides::where('project_id', $project_id)->first();
            $groom = Grooms::where('project_id', $project_id)->first();

            // Event
            $events = Events::where('project_id', $project_id)->first();

            // Hitung status completed
            $completedSubTodolists = SubTodolists::whereIn('todolist_id', Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project_id)->pluck('id'))->pluck('id'))->where('status', 1)->count();
            $totalCompleted = $completedSubTodolists;

            // Hitung status not completed
            $notCompletedSubTodolists = SubTodolists::whereIn('todolist_id', Todolists::whereIn('category_todolist_id', CategoryTodolists::where('project_id', $project_id)->pluck('id'))->pluck('id'))->where('status', 0)->count();
            $totalNotCompleted = $notCompletedSubTodolists;

            // Hitung total todolist
            $totalTask = $totalCompleted + $totalNotCompleted;

            // Hitung persentase
            $percentCompleted = $totalTask > 0 ? ($totalCompleted / $totalTask) * 100 : 0;

            // Budget
            $budget = Budgets::where('project_id', $project_id)->first();
            $totalBudget = $budget ? $budget->budget : 0;
            $paidBudget = $budget ? $budget->paid : 0;
            $unpaidBudget = $budget ? $budget->unpaid : 0;
            $remainingBudget = $budget ? $budget->budget - $budget->paid : 0;
            return response()->json(['message' => 'Fetch Data Successfully', 'information_bridegroom' => ['bride' => $bride, 'groom' => $groom], "events" => $events, 'progress_list' => ['totalTask' => $totalTask, 'totalCompleted' => $totalCompleted, 'totalNotCompleted' => $totalNotCompleted, 'persenCompleted' => $percentCompleted], 'budget' => ['totalBudget' => $totalBudget, 'paidBudget' => $paidBudget, 'unpaidBudget' => $unpaidBudget, 'remainingBudget' => $remainingBudget]], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
