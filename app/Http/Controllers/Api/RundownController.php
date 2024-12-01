<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rundowns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RundownController extends Controller
{
    public function getRundowns()
    {
        try {
            $Rundowns = Rundowns::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Rundowns], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getRundownsByProjectId($project_id)
    {
        try {
            $Rundowns = Rundowns::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Rundowns], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getRundownsById($id)
    {
        try {
            $Rundowns = Rundowns::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Rundowns], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createRundowns(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'time' => 'required',
                'title_event' => 'required',
                'minute' => 'required',
                'address' => 'required',
                'person_responsible' => 'required',
                'status' => 'required',
                'description' => 'required',
                'icon' => 'required',
                'vendor' => 'required',
            ]);
            $Rundowns = Rundowns::create([
                'project_id' => $request->project_id,
                'time' => $request->time,
                'title_event' => $request->title_event,
                'minute' => $request->minute,
                'address' => $request->address,
                'person_responsible' => $request->person_responsible,
                'description' => $request->description,
                'status' => $request->status,
                'icon' => $request->icon,
                'vendor' => $request->vendor,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Rundowns], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateRundowns(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'time' => 'required',
                'title_event' => 'required',
                'minute' => 'required',
                'address' => 'required',
                'person_responsible' => 'required',
                'status' => 'required',
                'description' => 'required',
                'icon' => 'required',
                'vendor' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $Rundowns = Rundowns::find($id);
            if (!$Rundowns) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            $Rundowns->update([
                'time' => $request->time,
                'title_event' => $request->title_event,
                'minute' => $request->minute,
                'address' => $request->address,
                'person_responsible' => $request->person_responsible,
                'description' => $request->description,
                'status' => $request->status,
                'icon' => $request->icon,
                'vendor' => $request->vendor,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Rundowns], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteRundowns($id)
    {
        try {
            Rundowns::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
