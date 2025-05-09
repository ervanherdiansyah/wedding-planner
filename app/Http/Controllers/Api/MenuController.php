<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getMenu()
    {
        try {
            $Menu = Menu::get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Menu], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getMenuByProjectId($project_id)
    {
        try {
            $Menu = Menu::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Menu], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getMenuById($id)
    {
        try {
            $Menu = Menu::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Menu], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createMenu(Request $request)
    {
        try {
            //code...
            Request()->validate([]);
            $Menu = Menu::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'icon' => $request->icon,
                'is_active' => $request->is_active,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Menu], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateMenu(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $Menu = Menu::find($id);
            if (!$Menu) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            $Menu->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'icon' => $request->icon,
                'is_active' => $request->is_active,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Menu], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteMenu($id)
    {
        try {
            Menu::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
