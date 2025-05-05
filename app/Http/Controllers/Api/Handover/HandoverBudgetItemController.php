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
    public function getAllHandoverBudgetByProjectId($project_id)
    {
        try {
            // Ambil data HandoverBudget beserta relasinya
            $handoverBudgets = HandoverBudget::where('project_id', $project_id)
                ->with(['categoryHandover.HandoverBudgetItem'])
                ->get();

            $resultMale = [];
            $resultFemale = [];

            $totalMale = 0;
            $buyMale = 0;
            $totalFemale = 0;
            $buyFemale = 0;

            foreach ($handoverBudgets as $handover) {
                foreach ($handover->categoryHandover as $category) {
                    $items = $category->HandoverBudgetItem;

                    // Filter male
                    $maleItems = $items->where('category', 'male')->values();
                    if ($maleItems->isNotEmpty()) {
                        $resultMale[] = [
                            'id' => $category->id,
                            'title' => $category->title,
                            'items' => $maleItems->map(function ($item) use ($category) {
                                return [
                                    'id' => $item->id,
                                    'handover_budgets_id' => $category->handover_budgets_id,
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

                    // Filter female
                    $femaleItems = $items->where('category', 'female')->values();
                    if ($femaleItems->isNotEmpty()) {
                        $resultFemale[] = [
                            'id' => $category->id,
                            'title' => $category->title,
                            'items' => $femaleItems->map(function ($item) use ($category) {
                                return [
                                    'id' => $item->id,
                                    'handover_budgets_id' => $category->handover_budgets_id,
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
                'HandoverBudgetItemMale' => $resultMale,
                'HandoverBudgetItemFemale' => $resultFemale,
                'total_category' => $handoverBudgets->count(),
                'total_item_male' => $totalMale,
                'total_buy_item_male' => $buyMale,
                'total_item_female' => $totalFemale,
                'total_buy_item_female' => $buyFemale,
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    // public function getHandoverBudgetItemByProjectId($project_id)
    // {
    //     try {
    //         // $user = Auth::user();
    //         // $project = Projects::where('user_id', $user->id)->first();
    //         $HandoverBudget = HandoverBudget::where('project_id', $project_id)
    //             ->with(['categoryHandover.HandoverBudgetItem']) // Nested eager loading
    //             ->get()
    //             ->map(function ($HandoverBudget) {
    //                 // Filter berdasarkan kategori

    //                 // Return data yang diperlukan
    //                 return [
    //                     // Total dan yang sudah dibeli berdasarkan kategori
    //                     "total_HandoverBudgetItem_male" => $MaleItems->count(),
    //                     "buy_HandoverBudgetItem_male" => $MaleItems->where('status', true)->count(),
    //                     "total_HandoverBudgetItem_female" => $FemaleItems->count(),
    //                     "buy_HandoverBudgetItem_female" => $FemaleItems->where('status', true)->count(),

    //                     // Data berdasarkan kategori
    //                     $resultMale = [];
    //     $resultFemale = [];

    //     foreach ($handoverBudgets as $handover) {
    //         foreach ($handover->categoryHandover as $category) {
    //             $items = $category->HandoverBudgetItem;

    //             // Filter kategori male
    //             $maleItems = $items->where('category', 'male')->values();
    //             if ($maleItems->isNotEmpty()) {
    //                 $resultMale[] = [
    //                     'id' => $category->id,
    //                     'title' => $category->title,
    //                     'items' => $maleItems->map(function ($item) {
    //                         return [
    //                             'id' => $item->id,
    //                             'handover_budgets_id' => $item->handover_budgets_id,
    //                             'name' => $item->name,
    //                             'category' => $item->category,
    //                             'purchase_method' => $item->purchase_method,
    //                             'price' => $item->price,
    //                             'detail' => $item->detail,
    //                             'purchase_date' => $item->purchase_date,
    //                             'status' => $item->status,
    //                         ];
    //                     }),
    //                 ];
    //             }

    //             // Filter kategori female
    //             $femaleItems = $items->where('category', 'female')->values();
    //             if ($femaleItems->isNotEmpty()) {
    //                 $resultFemale[] = [
    //                     'id' => $category->id,
    //                     'title' => $category->title,
    //                     'items' => $femaleItems->map(function ($item) {
    //                         return [
    //                             'id' => $item->id,
    //                             'handover_budgets_id' => $item->handover_budgets_id,
    //                             'name' => $item->name,
    //                             'category' => $item->category,
    //                             'purchase_method' => $item->purchase_method,
    //                             'price' => $item->price,
    //                             'detail' => $item->detail,
    //                             'purchase_date' => $item->purchase_date,
    //                             'status' => $item->status,
    //                         ];
    //                     }),
    //                 ];
    //             }
    //                 ];
    //             });

    //         $total_category = $HandoverBudget->count();
    //         $total_item_male = HandoverBudgetItem::where('category', "male")->count();
    //         $total_item_female = HandoverBudgetItem::where('category', "female")->count();
    //         $total_buy_item_male = HandoverBudgetItem::where('category', "male")->where('status', true)->count();
    //         $total_buy_item_female = HandoverBudgetItem::where('category', "female")->where('status', true)->count();

    //         return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudget, 'total_category' => $total_category, 'total_item_male' => $total_item_male, 'total_item_female' => $total_item_female, 'total_buy_item_male' => $total_buy_item_male, 'total_buy_item_female' => $total_buy_item_female], 200);
    //     } catch (\Exception $th) {
    //         return response()->json(['message' => $th->getMessage()], 500);
    //     }
    // }
}
