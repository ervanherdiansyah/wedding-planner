<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ListVendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListVendorController extends Controller
{
    public function getListVendors()
    {
        try {
            $ListVendors = ListVendors::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListVendors], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getListVendorsByCategoryVendorId($category_vendor_id)
    {
        try {
            $ListVendors = ListVendors::where('category_vendor_id', $category_vendor_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListVendors], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getListVendorsById($id)
    {
        try {
            $ListVendors = ListVendors::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListVendors], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createListVendors(Request $request)
    {
        try {
            //code...
            Request()->validate([]);

            $file_name = null;
            if ($request->hasFile('image')) {
                $file_name = $request->image->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->image->storeAs('public/image_vendor', $namaGambar);
            }
            $ListVendors = ListVendors::create([
                'category_vendor_id' => $request->category_vendor_id,
                'vendor_name' => $request->vendor_name,
                'vendor_price' => $request->vendor_price,
                'person_responsible' => $request->person_responsible,
                'vendor_contact' => $request->vendor_contact,
                'social_media' => $request->social_media,
                'vendor_features' => $request->vendor_features,
                'image' =>  $file_name ? "image_vendor/" . $namaGambar : null,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $ListVendors], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateListVendors(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data ListVendors berdasarkan ID
            $ListVendors = ListVendors::find($id);
            if (!$ListVendors) {
                return response()->json(['message' => 'ListVendors not found'], 404);
            }

            // Periksa jika ada file yang diunggah
            if ($request->hasFile('photo_ListVendors')) {
                // Hapus file lama jika ada
                if ($ListVendors->photo_ListVendors && Storage::exists('public/' . $ListVendors->photo_ListVendors)) {
                    Storage::delete('public/' . $ListVendors->photo_ListVendors);
                }

                // Simpan file baru
                $fileName = $request->file('image')->getClientOriginalName();
                $sanitizedFileName = str_replace(' ', '_', $fileName);
                $filePath = $request->file('image')->storeAs('public/image_vendor', $sanitizedFileName);

                // Update data ListVendors
                $ListVendors->update([
                    'vendor_name' => $request->vendor_name,
                    'vendor_price' => $request->vendor_price,
                    'person_responsible' => $request->person_responsible,
                    'vendor_contact' => $request->vendor_contact,
                    'social_media' => $request->social_media,
                    'vendor_features' => $request->vendor_features,
                    'image' => 'image_vendor/' . $sanitizedFileName,
                ]);
            } else {
                $ListVendors->update([
                    'vendor_name' => $request->vendor_name,
                    'vendor_price' => $request->vendor_price,
                    'person_responsible' => $request->person_responsible,
                    'vendor_contact' => $request->vendor_contact,
                    'social_media' => $request->social_media,
                    'vendor_features' => $request->vendor_features,
                ]);
            }

            // Return response sukses
            return response()->json(['message' => 'Updated Data successfully', 'data' => $ListVendors], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteListVendors($id)
    {
        try {
            ListVendors::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
