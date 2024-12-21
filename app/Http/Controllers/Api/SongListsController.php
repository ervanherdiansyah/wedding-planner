<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SongLists;
use Illuminate\Http\Request;

class SongListsController extends Controller
{
    public function getSongLists()
    {
        try {
            $SongLists = SongLists::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $SongLists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getSongListsByProjectId($project_id)
    {
        try {
            $SongLists = SongLists::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $SongLists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getSongListsById($id)
    {
        try {
            $SongLists = SongLists::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $SongLists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createSongLists(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
                'singer_name' => 'required',
                'title' => 'required',
            ]);

            $SongLists = SongLists::create([
                'project_id' => $request->project_id,
                'singer_name' => $request->singer_name,
                'title' => $request->title,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $SongLists], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateSongLists(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'singer_name' => 'required',
                'title' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $SongLists = SongLists::find($id);
            if (!$SongLists) {
                return response()->json(['message' => 'Song List not found'], 404);
            }
            // Update data bride
            $SongLists->update([
                'singer_name' => $request->singer_name,
                'title' => $request->title,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $SongLists], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteSongLists($id)
    {
        try {
            SongLists::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}