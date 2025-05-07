<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InviteUserMail;
use App\Models\ProjectMemberships;
use App\Models\Projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class MembershipController extends Controller
{
    public function inviteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|string'
        ]);

        $user = auth()->user();

        // Ambil project milik user (owner)
        $invited = Projects::where('id', $request->project_id)
            ->where('user_id', $user->id)
            ->first();
        $invitedCount = ProjectMemberships::where('project_id', $request->project_id)->count();

        if ($invitedCount >= $invited->invited_project) {
            return response()->json([
                'message' => 'Maksimal ' . $invited->invited_project . ' orang dapat diundang untuk project ini.'
            ], 400);
        }

        if (!$invited) {
            return response()->json([
                'message' => 'Kamu tidak memiliki izin untuk mengundang ke project ini.'
            ], 403);
        }

        $token = Str::random(32);

        ProjectMemberships::create([
            'project_id' => $request->project_id,
            'user_id' => null,
            'role' => $request->role,
            'token' => $token,
            'status' => 'invited'
        ]);

        // Kirim Email
        $inviteLink = 'https://weddingplanner-frontend.com/invite/form?token=' . $token;
        $projectName = "Wedding Planner";
        Mail::to($request->email)->send(new InviteUserMail($inviteLink, $projectName));

        return response()->json(['message' => 'Invitation sent']);
    }
}
