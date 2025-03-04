<?php

namespace App\Http\Controllers\Api\Handover;

use App\Http\Controllers\Controller;
use App\Models\HandoverBudget;
use App\Models\HandoverBudgetItem;
use Illuminate\Http\Request;

class HandoverBudgetItemController extends Controller
{
    public function getHandoverBudgetItem()
    {
        try {
            $HandoverBudgetItem = HandoverBudgetItem::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudgetItem], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getHandoverBudgetItemByHandoverBudgetId($handover_budgets_id)
    {
        try {
            $HandoverBudgetItem = HandoverBudgetItem::where('handover_budgets_id', $handover_budgets_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudgetItem], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getHandoverBudgetItemById($id)
    {
        try {
            $HandoverBudgetItem = HandoverBudgetItem::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudgetItem], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createHandoverBudgetItem(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'handover_budgets_id' => 'required',
            ]);

            $HandoverBudgetItem = HandoverBudgetItem::create([
                'handover_budgets_id' => $request->handover_budgets_id,
                'name' => $request->name,
                'category' => $request->category,
                'purchase_method' => $request->purchase_method,
                'price' => $request->price,
                'status' => 0,
                'purchase_date' => $request->purchase_date,
            ]);
            // Update used budget
            $HandoverBudget = HandoverBudget::find($HandoverBudgetItem->handover_budgets_id);
            if ($request->category == 'male') {
                $HandoverBudget->used_budget_male += $request->price;
            } else {
                $HandoverBudget->used_budget_female += $request->price;
            }
            $HandoverBudget->save();

            return response()->json(['message' => 'Create Data Successfully', 'data' => $HandoverBudgetItem], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateHandoverBudgetItem(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $HandoverBudgetItem = HandoverBudgetItem::find($id);
            if (!$HandoverBudgetItem) {
                return response()->json(['message' => 'Budget not found'], 404);
            }
            // Update data bride
            $HandoverBudgetItem->update([
                'name' => $request->name,
                'category' => $request->category,
                'purchase_method' => $request->purchase_method,
                'price' => $request->price,
                'status' => $request->status,
                'purchase_date' => $request->purchase_date,
            ]);

            $oldPrice = $HandoverBudgetItem->price;

            $HandoverBudget = HandoverBudget::find($HandoverBudgetItem->handover_budgets_id);
            if ($request->has('price')) {
                if ($HandoverBudgetItem->category == 'male') {
                    $HandoverBudget->used_budget_male = $HandoverBudget->used_budget_male - $oldPrice + $HandoverBudgetItem->price;
                } else {
                    $HandoverBudget->used_budget_female = $HandoverBudget->used_budget_female - $oldPrice + $HandoverBudgetItem->price;
                }
                $HandoverBudget->save();
            }
            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $HandoverBudgetItem], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteHandoverBudgetItem($id)
    {
        try {
            HandoverBudgetItem::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function getHandoverBudgetItemByProjectId($project_id)
    {
        try {
            // $user = Auth::user();
            // $project = Projects::where('user_id', $user->id)->first();
            $HandoverBudget = HandoverBudget::where('project_id', $project_id)
                ->with(['HandoverBudgetItem']) // Nested eager loading
                ->get()
                ->map(function ($HandoverBudget) {
                    // Filter berdasarkan kategori
                    $MaleItems = $HandoverBudget->HandoverBudgetItem->where('category', 'male');
                    $FemaleItems = $HandoverBudget->HandoverBudgetItem->where('category', 'female');
                    $diferent_male = $HandoverBudget->male_budget - $HandoverBudget->used_budget_male;
                    $diferent_female = $HandoverBudget->female_budget - $HandoverBudget->used_budget_female;

                    // Return data yang diperlukan
                    return [
                        'id' => $HandoverBudget->id,
                        'male_budget' => $HandoverBudget->male_budget,
                        'female_budget' => $HandoverBudget->female_budget,
                        "used_budget_male" => $HandoverBudget->used_budget_male,
                        "used_budget_female" => $HandoverBudget->used_budget_female,
                        "diferent_male" => $diferent_male,
                        "diferent_female" => $diferent_female,

                        // Total dan yang sudah dibeli berdasarkan kategori
                        "total_HandoverBudgetItem_male" => $MaleItems->count(),
                        "buy_HandoverBudgetItem_male" => $MaleItems->where('status', true)->count(),
                        "total_HandoverBudgetItem_female" => $FemaleItems->count(),
                        "buy_HandoverBudgetItem_female" => $FemaleItems->where('status', true)->count(),

                        // Data berdasarkan kategori
                        'HandoverBudgetItem_male' => $MaleItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'handover_budgets_id' => $item->handover_budgets_id,
                                'name' => $item->name,
                                'category' => $item->category,
                                'purchase_method' => $item->purchase_method,
                                'price' => $item->price,
                                'detail' => $item->detail,
                                'purchase_date' => $item->purchase_date,
                                'status' => $item->status,
                            ];
                        }),
                        'HandoverBudgetItem_female' => $FemaleItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'handover_budgets_id' => $item->handover_budgets_id,
                                'name' => $item->name,
                                'category' => $item->category,
                                'purchase_method' => $item->purchase_method,
                                'price' => $item->price,
                                'detail' => $item->detail,
                                'purchase_date' => $item->purchase_date,
                                'status' => $item->status,
                            ];
                        }),
                    ];
                });

            $total_category = $HandoverBudget->count();
            $total_item_male = HandoverBudgetItem::where('category', "male")->count();
            $total_item_female = HandoverBudgetItem::where('category', "female")->count();
            $total_buy_item_male = HandoverBudgetItem::where('category', "male")->where('status', true)->count();
            $total_buy_item_female = HandoverBudgetItem::where('category', "female")->where('status', true)->count();

            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudget, 'total_category' => $total_category, 'total_item_male' => $total_item_male, 'total_item_female' => $total_item_female, 'total_buy_item_male' => $total_buy_item_male, 'total_buy_item_female' => $total_buy_item_female], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
