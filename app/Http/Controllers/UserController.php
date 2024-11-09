<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get all conversations where the user is a participant
        $conversations = Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('participants')->get();

        // Collect the IDs of users already in conversations with the logged-in user
        $connectedUserIds = $conversations->flatMap(function ($conversation) use ($userId) {

            return $conversation->participants->pluck('id');
        })->unique()->filter(function ($id) use ($userId) {
            return $id !== $userId; // Exclude the logged-in user's own ID
        });

        // Get other users excluding the logged-in user and those already in conversations
        $users = User::whereNotIn('id', $connectedUserIds->all())
            ->where('id', '!=', $userId)
            ->get();

        return Inertia::render('Dashboard', [
            'conversations' => $conversations,
            'users' => $users,
            'currentUser' => Auth::user(),
        ]);
    }
}
