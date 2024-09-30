<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    
    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'type' => 'required|string',
    //         'title' => 'required|string|max:255',
    //         'message' => 'required|string',
    //     ]);

    //     $notification = Notification::create([
    //         'uuid' => Str::uuid(),
    //         'user_id' => $request->user_id,
    //         'type' => $request->type,
    //         'title' => $request->title,
    //         'message' => $request->message,
    //         'is_read' => false,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Notification created successfully',
    //         'notification' => $notification
    //     ]);
    // }

    // public function show($uuid)
    // {
    //     $notification = Notification::where('uuid', $uuid)->firstOrFail();

    //     return response()->json([
    //         'success' => true,
    //         'notification' => $notification
    //     ]);
    // }


    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)->first();

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
            'notification' => $notification
        ]);
    }

    public function markAsUnRead($id)
    {
        $notification = Notification::where('id', $id)->first();

        if ($notification->is_read) {
            $notification->markAsUnRead();
        }

        return response()->json([
            'status' => true,
            'message' => 'Notification un read successfully',
            'notification' => $notification
        ]);
    }

    public function destroy($id)
    {
        $notification = Notification::where('id', $id)->first();

        $notification->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
}
