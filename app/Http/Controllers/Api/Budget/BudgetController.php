<?php

namespace App\Http\Controllers\Api\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budgets;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function getBudgets()
    {
        try {
            $Budgets = Budgets::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Budgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getBudgetsByProjectId(Request $request, $project_id)
    {
        try {
            $budgets = Budgets::where('project_id', $project_id)
                ->with(['categoryBudget.listBudget.detailPaymentBudget'])
                ->get()
                ->map(function ($budget) use ($request) {
                    // Filter categoryBudget by title jika ada parameter search
                    $categoryBudgets = $budget->categoryBudget;
                    if ($request->has('search') && $request->search != '') {
                        $search = strtolower($request->search);
                        $categoryBudgets = $categoryBudgets->filter(function ($category) use ($search) {
                            return stripos($category->title, $search) !== false;
                        })->values();
                    }

                    $totalCategory = $categoryBudgets->count();
                    $totalList = $categoryBudgets->flatMap(function ($category) {
                        return $category->listBudget;
                    })->count();

                    $totalListStatusTrue = $categoryBudgets->flatMap(function ($category) {
                        return $category->listBudget->where('status', true);
                    })->count();

                    // Summary by category
                    $summary = $categoryBudgets->map(function ($category) {
                        $totalBudget = $category->listBudget->sum('actual_payment');
                        return [
                            'category' => $category->title,
                            'totalBudget' => $totalBudget,
                        ];
                    });
                    return [
                        'id' => $budget->id,
                        'project_id' => $budget->project_id,
                        'budget' => $budget->budget,
                        'estimated_payment' => $budget->estimated_payment,
                        'actual_payment' => $budget->actual_payment,
                        'paid' => $budget->paid,
                        'unpaid' => $budget->unpaid,
                        'difference' => $budget->difference,
                        'balance' => $budget->balance,
                        'total_category_budgets' => $totalCategory,
                        'total_list_budgets' => $totalList,
                        'total_status_true_list_budgets' => $totalListStatusTrue,
                        'category_budgets' => $categoryBudgets->map(function ($category) {
                            return [
                                'id' => $category->id,
                                'budget_id' => $category->budget_id,
                                'title' => $category->title,
                                'list_budgets' => $category->listBudget->map(function ($list) {
                                    return [
                                        'id' => $list->id,
                                        'category_budget_id' => $list->category_budget_id,
                                        'title' => $list->title,
                                        'estimated_payment' => $list->estimated_payment,
                                        'actual_payment' => $list->actual_payment,
                                        'difference' => $list->difference,
                                        'paid' => $list->paid,
                                        'remaining_payment' => $list->remaining_payment,
                                        'status' => (bool) $list->status,
                                        'detail_payment_budgets' => $list->detailPaymentBudget->map(function ($detail) {
                                            return [
                                                'id' => $detail->id,
                                                'list_budgets_id' => $detail->list_budgets_id,
                                                'description' => $detail->description,
                                                'deadline' => $detail->deadline,
                                                'paid' => $detail->paid,
                                                'payer' => $detail->payer,
                                                'date_payment' => $detail->date_payment,
                                                'type' => $detail->type,
                                            ];
                                        }),
                                    ];
                                }),
                            ];
                        }),
                        'summary' => $summary,
                    ];
                });

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $budgets
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function getBudgetsById($id)
    {
        try {
            $Budgets = Budgets::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Budgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createBudgets(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $Budgets = Budgets::create([
                'project_id' => $request->project_id,
                'budget' => $request->budget,
                'actual_payment' => $request->actual_payment,
                'estimated_payment' => $request->estimated_payment,
                'paid' => 0,
                'unpaid' => 0,
                'difference' => $request->estimated_payment -  $request->actual_payment,
                'balance' => 0,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Budgets], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateBudgets(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $Budgets = Budgets::find($id);
            if (!$Budgets) {
                return response()->json(['message' => 'Budget not found'], 404);
            }
            // Update data bride
            $Budgets->update([
                'budget' => $request->budget,
                'actual_payment' => $request->actual_payment,
                'estimated_payment' => $request->estimated_payment,
                'difference' => $request->estimated_payment - $request->actual_payment,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Budgets], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteBudgets($id)
    {
        try {
            Budgets::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
