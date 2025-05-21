<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuPackage;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function getPackage()
    {
        try {
            $Package = Package::get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Package], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getPackageByProjectId($project_id)
    {
        try {
            $Package = Package::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Package], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getPackageById($id)
    {
        try {
            $Package = Package::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Package], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createPackage(Request $request)
    {
        try {
            //code...
            Request()->validate([]);
            $Package = Package::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            foreach ($request['access'] as $access) {
                MenuPackage::create([
                    'menu_id' => $access['menu_id'],
                    'package_id' => $Package->id,
                ]);
            }

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Package], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updatePackage(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $Package = Package::find($id);
            if (!$Package) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            $Package->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            MenuPackage::where('package_id', $Package->id)->delete();
            foreach ($request['access'] as $access) {
                MenuPackage::create([
                    'menu_id' => $access['menu_id'],
                    'package_id' => $Package->id,
                ]);
            }

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Package], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deletePackage($id)
    {
        try {
            Package::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function createPackageMenu(Request $request)
    {
        try {
            //code...

            foreach ($request['access'] as $access) {
                MenuPackage::create([
                    'menu_id' => $access['menu_id'],
                    'package_id' => $access['package_id'],
                ]);
            }

            return response()->json(['message' => 'Create Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
