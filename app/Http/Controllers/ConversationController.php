<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Events\UserTyping;
use App\Http\Requests\ConversationRequest;
use App\Http\Requests\ConversationUserRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ConversationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //
    }

    /**
     * Display a listing of the user's conversations.
     */
    public function index()
    {
        $userId = Auth::id();

        // Get all conversations where the user is a participant
        $conversations = Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with([
            'participants',
            'messages' => function ($query) {
            $query->latest()->limit(1); // Load only the last message
        }])->get();

        return Inertia::render('Chats', [
            'conversations' => $conversations,
            'users' => User::all(),
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * Show the messages for a specific conversation.
     */
    public function show($id)
    {
        $userId = Auth::id();
        // Get all conversations where the user is a participant
        $conversations = Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with([
            'participants',
            'messages' => function ($query) {
                $query->latest()->limit(1); // Load only the last message
        }])->get();

        $conversation = Conversation::with(['messages.user', 'participants'])->findOrFail($id);
        Gate::authorize('view',$conversation);

        return Inertia::render('Chat', [
            'conversations' => $conversations,
            'conversation' => $conversation,
            'users' => User::all(),
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * Create a new conversation (one-on-one or group).
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:private,group',
            'name' => 'nullable|string|max:255', // Only used for group conversations
            'participant_ids' => 'required|array|min:1', // Array of user IDs
            'participant_ids.*' => 'exists:users,id',
        ]);

        $conversation = new Conversation();
        $conversation->type = $request->type;
        $conversation->created_by = Auth::id();

        if ($request->type === 'group') {
            $conversation->name = $request->name;
        }

        $conversation->save();

        // Add participants to conversation
        $participantIds = $request->participant_ids;
        $participantIds[] = Auth::id(); // Add the creator as a participant
        $conversation->participants()->sync($participantIds);

        return redirect()->route('conversations.show',['id' => $conversation->id]);
    }

    /**
     * Soft delete a conversation.
     */
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);

        // Authorization: Ensure user is a participant
        Gate::authorize('view',$conversation);

        // Soft delete the conversation
        $conversation->delete();

        return redirect()->route('dashboard')->with('message', 'Conversation deleted successfully.');
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        $conversation = Conversation::findOrFail($conversationId);

        // Authorization: Ensure user is a participant
        Gate::authorize('view',$conversation);

        // Create the message
        $message = new Message();
        $message->conversation_id = $conversationId;
        $message->user_id = Auth::id();
        $message->message = $request->message;
        $message->status = 'sent';
        $message->save();

        broadcast(new NewMessage($message))->toOthers();

        // Broadcast or notify participants of the new message (if real-time is implemented)

        return back();
    }

    /**
     * Add a participant to a group conversation.
     */
    public function addParticipant(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Ensure it's a group conversation
        if ($conversation->type !== 'group') {
            abort(403, 'Only group conversations can have multiple participants.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Add the user as a participant if not already added
        if (!$conversation->participants->contains($request->user_id)) {
            $conversation->participants()->attach($request->user_id);
        }

        return back();
    }

    public function userTyping($conversationId)
    {
        // Broadcast the typing event to the conversation
        broadcast(new UserTyping($conversationId, Auth::id()))->toOthers();

        return back();
    }
}
