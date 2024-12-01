<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Events;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function getEvents()
    {
        try {
            $Events = Events::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Events], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getEventsByProjectId($project_id)
    {
        try {
            $Events = Events::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Events], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getEventsById($id)
    {
        try {
            $Events = Events::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Events], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getEventsWedding($id)
    {
        try {
            $Events = Events::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Events], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createEvents(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'image' => 'required|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
                'bridegroom_name' => 'required',
                'event_name' => 'required',
                'event_datetime' => 'required',
                'address' => 'required',
                'description' => 'required',
            ]);

            $file_name = null;
            if ($request->hasFile('image')) {
                $file_name = $request->image->getClientOriginalName();
                $namaGambar = str_replace(' ', '_', $file_name);
                $image = $request->image->storeAs('public/image', $namaGambar);
            }
            $Events = Events::create([
                'project_id' => $request->project_id,
                'bridegroom_name' => $request->bridegroom_name,
                'event_name' => $request->event_name,
                'event_datetime' => $request->event_datetime,
                'address' => $request->address,
                'description' => $request->description,
                'image' =>  $file_name ? "image/" . $namaGambar : null,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Events], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateEvents(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'image' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
                'bridegroom_name' => 'required',
                'event_name' => 'required',
                'event_datetime' => 'required',
                'address' => 'required',
                'description' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $Events = Events::find($id);
            if (!$Events) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            // Periksa jika ada file yang diunggah
            if ($request->hasFile('image')) {
                // Hapus file lama jika ada
                if ($Events->image && Storage::exists('public/' . $Events->image)) {
                    Storage::delete('public/' . $Events->image);
                }

                // Simpan file baru
                $fileName = $request->file('image')->getClientOriginalName();
                $sanitizedFileName = str_replace(' ', '_', $fileName);
                $filePath = $request->file('image')->storeAs('public/image', $sanitizedFileName);

                // Update data bride
                $Events->update([
                    'bridegroom_name' => $request->bridegroom_name,
                    'event_name' => $request->event_name,
                    'event_datetime' => $request->event_datetime,
                    'address' => $request->address,
                    'description' => $request->description,
                    'image' => 'image/' . $sanitizedFileName,
                ]);
            } else {
                $Events->update([
                    'project_id' => $request->project_id,
                    'bridegroom_name' => $request->bridegroom_name,
                    'event_name' => $request->event_name,
                    'event_datetime' => $request->event_datetime,
                    'address' => $request->address,
                    'description' => $request->description,
                ]);
            }

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Events], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteEvents($id)
    {
        try {
            Events::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
