<?php

namespace App\Http\Controllers\Api\Handover;

use App\Http\Controllers\Controller;
use App\Models\CategoryHandover;
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
    public function getHandoverBudgetItemByCategoryHandoverBudgetsId($category_handover_budgets_id)
    {
        try {
            $HandoverBudgetItem = HandoverBudgetItem::where('category_handover_budgets_id', $category_handover_budgets_id)->get();
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
                'category_handover_budgets_id' => 'required',
            ]);

            $HandoverBudgetItem = HandoverBudgetItem::create([
                'category_handover_budgets_id' => $request->category_handover_budgets_id,
                'name' => $request->name,
                'category' => $request->category,
                'purchase_method' => $request->purchase_method,
                'price' => $request->price,
                'detail' => $request->detail,
                'status' => 0,
                'purchase_date' => $request->purchase_date,
            ]);
            // Update used budget
            $category_handover_budgets_id = CategoryHandover::where('id', $request->category_handover_budgets_id)->first();
            $HandoverBudget = HandoverBudget::find($category_handover_budgets_id->handover_budgets_id);
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
            $oldPrice = $HandoverBudgetItem->price;
            $HandoverBudgetItem->update([
                'name' => $request->name,
                'category' => $request->category,
                'purchase_method' => $request->purchase_method,
                'price' => $request->price,
                'status' => $request->status,
                'detail' => $request->detail,
                'purchase_date' => $request->purchase_date,
            ]);
            $category_handover_budgets_id = CategoryHandover::where('id', $request->category_handover_budgets_id)->first();
            $HandoverBudget = HandoverBudget::find($category_handover_budgets_id->handover_budgets_id);
            if ($HandoverBudgetItem->category == 'male') {
                $HandoverBudget->used_budget_male = $HandoverBudget->used_budget_male - $oldPrice + $HandoverBudgetItem->price;
                $HandoverBudget->save();
            } else {
                $HandoverBudget->used_budget_female = $HandoverBudget->used_budget_female - $oldPrice + $HandoverBudgetItem->price;
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
            $item = HandoverBudgetItem::where('id', $id)->first();
            $category_handover_budgets_id = CategoryHandover::where('id', $item->category_handover_budgets_id)->first();
            $HandoverBudget = HandoverBudget::find($category_handover_budgets_id->handover_budgets_id);

            // Kurangi dari used budget
            if ($item->category == 'male') {
                $HandoverBudget->used_budget_male -= $item->price;
            } else {
                $HandoverBudget->used_budget_female -= $item->price;
            }
            $HandoverBudget->save();
            $item->delete();

            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getAllHandoverBudgetByProjectId(Request $request, $project_id)
    {
        try {
            $query = HandoverBudget::where('project_id', $project_id)
                ->with(['categoryHandover.HandoverBudgetItem']);

            // Filter by gender (male / female)
            if ($request->has('gender') && in_array($request->gender, ['male', 'female'])) {
                $query->whereHas('categoryHandover', function ($q) use ($request) {
                    $q->where('type', $request->gender);
                });
            }

            // Filter by status (0/1 atau sesuai data)
            if ($request->has('status') && $request->status !== '') {
                $query->whereHas('categoryHandover.HandoverBudgetItem', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            $handoverBudgets = $query->get();

            // Ambil budget pertama (karena sepertinya satu project_id satu budget)
            $HandoverBudget = $handoverBudgets->first();

            $male_budget = $HandoverBudget ? $HandoverBudget->male_budget : null;
            $female_budget = $HandoverBudget ? $HandoverBudget->female_budget : null;
            $actual_male_budget = $HandoverBudget ? $HandoverBudget->used_budget_male : null;
            $actual_female_budget = $HandoverBudget ? $HandoverBudget->used_budget_female : null;

            $diferent_male = ($male_budget !== null && $actual_male_budget !== null)
                ? $male_budget - $actual_male_budget
                : null;

            $diferent_female = ($female_budget !== null && $actual_female_budget !== null)
                ? $female_budget - $actual_female_budget
                : null;

            $resultMale = [];
            $resultFemale = [];
            $totalMale = $buyMale = $totalFemale = $buyFemale = 0;
            $totalCategoryMale = $totalCategoryFemale = 0;

            foreach ($handoverBudgets as $handover) {
                foreach ($handover->categoryHandover as $category) {
                    $items = $category->HandoverBudgetItem;

                    // Filter by name
                    if ($request->has('name') && $request->name != '') {
                        $items = $items->filter(function ($item) use ($request) {
                            return stripos($item->name, $request->name) !== false;
                        });
                    }

                    // Filter by category
                    if ($request->has('category') && $request->category != '') {
                        $items = $items->where('category', $request->category);
                    }

                    $items = $items->values();

                    // Male
                    $maleItems = $items->where('category', 'male')->values();
                    if ($category->type === 'male') {
                        $totalCategoryMale++;
                        $resultMale[] = [
                            'id' => $category->id,
                            'handover_budgets_id' => $category->handover_budgets_id,
                            'title' => $category->title,
                            'items' => $maleItems->map(function ($item) use ($category) {
                                return [
                                    'id' => $item->id,
                                    'category_handover_budgets_id' => $category->id,
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
                        $totalMale += $maleItems->count();
                        $buyMale += $maleItems->where('status', true)->count();
                    }

                    // Female
                    $femaleItems = $items->where('category', 'female')->values();
                    if ($category->type === 'female') {
                        $totalCategoryFemale++;
                        $resultFemale[] = [
                            'id' => $category->id,
                            'handover_budgets_id' => $category->handover_budgets_id,
                            'title' => $category->title,
                            'items' => $femaleItems->map(function ($item) use ($category) {
                                return [
                                    'id' => $item->id,
                                    'category_handover_budgets_id' => $category->id,
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
                        $totalFemale += $femaleItems->count();
                        $buyFemale += $femaleItems->where('status', true)->count();
                    }
                }
            }

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => [
                    'id' => $HandoverBudget ? $HandoverBudget->id : null,
                    'HandoverBudgetItemMale' => [
                        'male_budget' => $male_budget,
                        'actual_male_budget' => $actual_male_budget,
                        'diferent_male' => $diferent_male,
                        'totalCategoryMale' => $totalCategoryMale,
                        'total_item_male' => $totalMale,
                        'total_buy_item_male' => $buyMale,
                        'listHandover' => $resultMale,
                    ],
                    'HandoverBudgetItemFemale' => [
                        'female_budget' => $female_budget,
                        'actual_female_budget' => $actual_female_budget,
                        'diferent_female' => $diferent_female,
                        'totalCategoryFemale' => $totalCategoryFemale,
                        'total_item_female' => $totalFemale,
                        'total_buy_item_female' => $buyFemale,
                        'listHandover' => $resultFemale,
                    ],
                ],
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
