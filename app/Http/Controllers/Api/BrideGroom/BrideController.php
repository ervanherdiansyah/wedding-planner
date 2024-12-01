<?php

namespace App\Http\Controllers\Api\BrideGroom;

use App\Http\Controllers\Controller;
use App\Models\Brides;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrideController extends Controller
{
    public function getBride()
    {
        try {
            $bride = Brides::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $bride], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getBrideByProjectId($project_id)
    {
        try {
            $bride = Brides::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $bride], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getBrideById($id)
    {
        try {
            $bride = Brides::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $bride], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createBride(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'photo_bride' => 'required|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
                'name_bride' => 'required',
                'child_bride' => 'required',
                'father_name_bride' => 'required',
                'mother_name_bride' => 'required',
            ]);

            $file_name = null;
            if ($request->hasFile('photo_bride')) {
                $file_name = $request->photo_bride->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $photo_bride = $request->photo_bride->storeAs('public/photo_bride', $namaGambar);
            }
            $bride = Brides::create([
                'project_id' => $request->project_id,
                'name_bride' => $request->name_bride,
                'child_bride' => $request->child_bride,
                'father_name_bride' => $request->father_name_bride,
                'mother_name_bride' => $request->mother_name_bride,
                'photo_bride' =>  $file_name ? "photo_bride/" . $namaGambar : null,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $bride], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateBride(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'name_bride' => 'required|string|max:255',
                'child_bride' => 'required|string|max:255',
                'father_name_bride' => 'required|string|max:255',
                'mother_name_bride' => 'required|string|max:255',
                'photo_bride' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
            ]);

            // Cari data bride berdasarkan ID
            $bride = Brides::find($id);
            if (!$bride) {
                return response()->json(['message' => 'Bride not found'], 404);
            }

            // Periksa jika ada file yang diunggah
            if ($request->hasFile('photo_bride')) {
                // Hapus file lama jika ada
                if ($bride->photo_bride && Storage::exists('public/' . $bride->photo_bride)) {
                    Storage::delete('public/' . $bride->photo_bride);
                }

                // Simpan file baru
                $fileName = $request->file('photo_bride')->getClientOriginalName();
                $sanitizedFileName = str_replace(' ', '_', $fileName);
                $filePath = $request->file('photo_bride')->storeAs('public/photo_bride', $sanitizedFileName);

                // Update data bride
                $bride->update([
                    'name_bride' => $request->name_bride,
                    'child_bride' => $request->child_bride,
                    'father_name_bride' => $request->father_name_bride,
                    'mother_name_bride' => $request->mother_name_bride,
                    'photo_bride' => 'photo_bride/' . $sanitizedFileName,
                ]);
            } else {
                $bride->update([
                    'name_bride' => $request->name_bride,
                    'child_bride' => $request->child_bride,
                    'father_name_bride' => $request->father_name_bride,
                    'mother_name_bride' => $request->mother_name_bride,
                ]);
            }

            // Return response sukses
            return response()->json(['message' => 'Bride updated successfully', 'data' => $bride], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteBride($id)
    {
        try {
            Brides::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
