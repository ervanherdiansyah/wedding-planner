<?php

namespace App\Http\Controllers\Api\BrideGroom;

use App\Http\Controllers\Controller;
use App\Models\Grooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroomController extends Controller
{
    public function getGroom()
    {
        try {
            $groom = Grooms::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $groom], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getGroomByProjectId($project_id)
    {
        try {
            $groom = Grooms::where('project_id', $project_id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $groom], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getGroomById($id)
    {
        try {
            $groom = Grooms::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $groom], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createGroom(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'photo_groom' => 'required|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
                'name_groom' => 'required',
                'child_groom' => 'required',
                'father_name_groom' => 'required',
                'mother_name_groom' => 'required',
            ]);

            $file_name = null;
            if ($request->hasFile('photo_groom')) {
                $file_name = $request->photo_groom->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $photo_groom = $request->photo_groom->storeAs('public/photo_groom', $namaGambar);
            }
            $groom = Grooms::create([
                'project_id' => $request->project_id,
                'name_groom' => $request->name_groom,
                'child_groom' => $request->child_groom,
                'father_name_groom' => $request->father_name_groom,
                'mother_name_groom' => $request->mother_name_groom,
                'photo_groom' =>  $file_name ? "photo_groom/" . $namaGambar : null,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $groom], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateGroom(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'name_groom' => 'required|string|max:255',
                'child_groom' => 'required|string|max:255',
                'father_name_groom' => 'required|string|max:255',
                'mother_name_groom' => 'required|string|max:255',
                'photo_groom' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
            ]);

            // Cari data groom berdasarkan ID
            $groom = Grooms::find($id);
            if (!$groom) {
                return response()->json(['message' => 'Groom not found'], 404);
            }

            // Periksa jika ada file yang diunggah
            if ($request->hasFile('photo_groom')) {
                // Hapus file lama jika ada
                if ($groom->photo_groom && Storage::exists('public/' . $groom->photo_groom)) {
                    Storage::delete('public/' . $groom->photo_groom);
                }

                // Simpan file baru
                $fileName = $request->file('photo_groom')->getClientOriginalName();
                $sanitizedFileName = str_replace(' ', '_', $fileName);
                $filePath = $request->file('photo_groom')->storeAs('public/photo_groom', $sanitizedFileName);

                // Update data groom
                $groom->update([
                    'name_groom' => $request->name_groom,
                    'child_groom' => $request->child_groom,
                    'father_name_groom' => $request->father_name_groom,
                    'mother_name_groom' => $request->mother_name_groom,
                    'photo_groom' => 'photo_groom/' . $sanitizedFileName,
                ]);
            } else {
                $groom->update([
                    'name_groom' => $request->name_groom,
                    'child_groom' => $request->child_groom,
                    'father_name_groom' => $request->father_name_groom,
                    'mother_name_groom' => $request->mother_name_groom,
                ]);
            }

            // Return response sukses
            return response()->json(['message' => 'groom updated successfully', 'data' => $groom], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteGroom($id)
    {
        try {
            Grooms::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
