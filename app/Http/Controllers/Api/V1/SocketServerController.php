<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Socket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class SocketServerController extends Controller
{
      public function socket(Request $request): JsonResponse
      {
        $validatedData = $request->validate([
            "user_id" => 'nullable',
            "socket_id" => 'nullable|string',
        ]);


        $socket = Socket::create([
            "user_id" => $validatedData['user_id'],
            "socket_id" => $validatedData['socket_id'],
        ]);


        return response()->json([
            "status" => true,
            "message" => "SOcket  Uploaded Successfully",
        ]);

    }

     public function getSocket(Request $request): JsonResponse
    {
         $socket = Socket::orderBy('created_at', 'desc')
            ->with('user')
            ->get();

         return response()->json([
            "status" => true,
            "data" => $socket,
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'socket_id' => 'required'
        ]);

        // Get the socket by id
        $socket = Socket::find($request->input('socket_id'));

        // Check if the socket exists
        if (!$socket) {
            return response()->json([
                "status" => false,
                "message" => "Socket not found"
            ]);
        }

        // Delete the socket
        $socket->delete();

        return response()->json([
            "status" => true,
            "message" => "Socket deleted successfully"
        ]);
    }

}
