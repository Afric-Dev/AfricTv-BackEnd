<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    
    public function index()
    {
        $userId = Auth::id();
        $notifications = Notification::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

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

    public function show($uuid)
    {
        $notification = Notification::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'success' => true,
            'notification' => $notification
        ]);
    }


    public function markAsRead($uuid)
    {
        $notification = XNotification::where('uuid', $uuid)->firstOrFail();

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'notification' => $notification
        ]);
    }

    public function destroy($uuid)
    {
        $notification = XNotification::where('uuid', $uuid)->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
}
