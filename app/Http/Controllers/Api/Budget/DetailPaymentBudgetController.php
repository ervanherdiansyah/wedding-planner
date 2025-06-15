<?php

namespace App\Http\Controllers\Api\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budgets;
use App\Models\CategoryBudgets;
use App\Models\DetailPaymentBudget;
use App\Models\ListBudgets;
use Illuminate\Http\Request;

class DetailPaymentBudgetController extends Controller
{
    public function getDetailPaymentBudget()
    {
        try {
            $DetailPaymentBudget = DetailPaymentBudget::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $DetailPaymentBudget], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getDetailPaymentBudgetByListBudgetId($list_budgets_id)
    {
        try {
            $DetailPaymentBudget = DetailPaymentBudget::where('list_budgets_id', $list_budgets_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $DetailPaymentBudget], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getDetailPaymentBudgetById($id)
    {
        try {
            $DetailPaymentBudget = DetailPaymentBudget::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $DetailPaymentBudget], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createDetailPaymentBudget(Request $request)
    {
        try {
            // Simpan data pembayaran detail
            $detail = DetailPaymentBudget::create([
                'list_budgets_id' => $request->list_budgets_id,
                'description' => $request->description,
                'paid' => $request->paid,
                'payer' => $request->payer,
                'date_payment' => $request->date_payment,
                'deadline' => $request->deadline,
                'type' => $request->type,
            ]);

            // Ambil list budget terkait
            $listBudget = ListBudgets::findOrFail($request->list_budgets_id);

            // Tambahkan total paid baru ke list budget
            $listBudget->paid += $request->paid;
            $listBudget->remaining_payment = max(0, $listBudget->actual_payment - $listBudget->paid); // contoh logika unpaid
            $listBudget->save();

            $categoryBudget = CategoryBudgets::findOrFail($listBudget->category_budget_id);
            // Ambil budget utama (asumsinya ada relasi list_budget->budget_id)
            $budget = Budgets::findOrFail($categoryBudget->budget_id);
            $budget->paid += $request->paid;
            $budget->unpaid = max(0, $budget->actual_payment - $budget->paid); // contoh logika unpaid
            $budget->save();

            return response()->json([
                'message' => 'Create Detail Payment and Update Budget Successfully',
                'data' => $detail
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateDetailPaymentBudget(Request $request, $id)
    {
        try {
            // Ambil data detail payment yang akan diupdate
            $detail = DetailPaymentBudget::findOrFail($id);

            // Simpan nilai paid sebelumnya
            $oldPaid = $detail->paid;

            // Update data detail
            $detail->update([
                'description' => $request->description,
                'paid' => $request->paid,
                'payer' => $request->payer,
                'date_payment' => $request->date_payment,
                'deadline' => $request->deadline,
                'type' => $request->type,
            ]);

            // Hitung selisih paid
            $diff = $request->paid - $oldPaid;

            // Update List Budget
            $listBudget = ListBudgets::findOrFail($detail->list_budgets_id);
            $listBudget->paid += $diff;
            $listBudget->remaining_payment = max(0, $listBudget->actual_payment - $listBudget->paid);
            $listBudget->save();

            $categoryBudget = CategoryBudgets::findOrFail($listBudget->category_budget_id);

            // Update Budget
            $budget = Budgets::findOrFail($categoryBudget->budget_id);
            $budget->paid += $diff;
            $budget->unpaid = max(0, $budget->actual_payment - $budget->paid);
            $budget->save();

            return response()->json([
                'message' => 'Detail Payment Budget updated successfully',
                'data' => $detail
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function deleteDetailPaymentBudget($id)
    {
        try {
            // Ambil detail payment yang akan dihapus
            $detail = DetailPaymentBudget::findOrFail($id);

            // Simpan nilai paid yang akan dikurangi
            $paidAmount = $detail->paid;

            // Ambil list budget terkait
            $listBudget = ListBudgets::findOrFail($detail->list_budgets_id);

            $categoryBudget = CategoryBudgets::findOrFail($listBudget->category_budget_id);

            // Update Budget
            $budget = Budgets::findOrFail($categoryBudget->budget_id);

            // Kurangi nilai paid dari list budget dan budget utama
            $listBudget->paid = max(0, $listBudget->paid - $paidAmount);
            $listBudget->remaining_payment = max(0, $listBudget->actual_payment - $listBudget->paid);
            $listBudget->save();

            $budget->paid = max(0, $budget->paid - $paidAmount);
            $budget->unpaid = max(0, $budget->actual_payment - $budget->paid);
            $budget->save();

            // Hapus detail payment
            $detail->delete();

            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
