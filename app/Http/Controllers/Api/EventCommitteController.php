<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventCommittees;
use Illuminate\Http\Request;

class EventCommitteController extends Controller
{
    public function getEventCommittees()
    {
        try {
            $EventCommittees = EventCommittees::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $EventCommittees], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getEventCommitteesByProjectId($project_id, Request $request)
    {
        try {
            // Ambil parameter pencarian jika ada
            $search = $request->query('search');

            $query = EventCommittees::where('project_id', $project_id);

            // Jika ada parameter pencarian, tambahkan ke query
            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('contact', 'like', "%{$search}%");
            }

            $EventCommittees = $query->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $EventCommittees], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getEventCommitteesById($id)
    {
        try {
            $EventCommittees = EventCommittees::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $EventCommittees], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createEventCommittees(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $EventCommittees = EventCommittees::create([
                'project_id' => $request->project_id,
                'role' => $request->role,
                'name' => $request->name,
                'contact' => $request->contact,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $EventCommittees], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateEventCommittees(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $EventCommittees = EventCommittees::find($id);
            if (!$EventCommittees) {
                return response()->json(['message' => 'Event Committe not found'], 404);
            }
            // Update data bride
            $EventCommittees->update([
                'role' => $request->role,
                'name' => $request->name,
                'contact' => $request->contact,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $EventCommittees], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteEventCommittees($id)
    {
        try {
            EventCommittees::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
