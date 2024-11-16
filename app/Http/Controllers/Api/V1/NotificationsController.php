<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse; 
use App\Models\Educational;
use App\Models\Post;

class NotificationsController extends Controller
{
        
    public function index(): JsonResponse
    {
        $user = Auth::user();

        // Fetch notifications for the authenticated user
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Process notifications to include related post and user data
        $processedNotifications = $notifications->map(function ($notification) {
            if ($notification->type === 'BLOG POST' && isset($notification->post_id)) {
                $post = Post::where('id', $notification->post_id)->first();
                if ($post) {
                    $notification->related_post = $post;
                    $notification->post_user = $post->user; 
                }
            } elseif ($notification->type === 'EDUCATIONAL POST' && isset($notification->edu_id)) {
                $eduPost = Educational::where('id', $notification->edu_id)->first();
                if ($eduPost) {
                    $notification->related_post = $eduPost;
                    $notification->post_user = $eduPost->user;
                }
            }
            return $notification;
        });

        return response()->json([
            'success' => true,
            'notifications' => $processedNotifications
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


    public function markAsRead($id): JsonResponse
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

    public function markAsUnRead($id): JsonResponse
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

    public function destroy($id): JsonResponse
    {
        $notification = Notification::where('id', $id)->first();

        $notification->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }
}
