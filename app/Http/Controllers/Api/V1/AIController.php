<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\AI;
use Carbon\Carbon;

class AIController extends Controller
{
    public function AI(Request $request)
    {

        $request->validate([
                "message" => "required|max:255",
        ]);
        $userId = auth()->id(); 

        // Retrieve the user's count in the last 24 hours
        $userCount = AI::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subDay()) // 24 hours
            ->first();

        // If user count exists and is 10 or more
        if ($userCount && $userCount->count >= 10) {
            return response()->json(['message' => 'You have reached your daily limit'], 403);
        }

        // If count is less than 10, perform the action
        if ($userCount) {
            $userCount->count += 1; // Increment the count
            $userCount->save();
            //Perfrom AI action
            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => 'Hello!'],
                ],
            ]);
            return response->json([
                "status" => true,
                "data" => $result->choices[0]->message->content,
            ]);

        } else {
            // If no record exists for the last 24 hours, create a new one
            AI::create([
                'user_id' => $userId,
                'count' => 1,
            ]);
            //Perfrom AI action
            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => 'Hello!'],
                ],
            ]);

            return response->json([
                "status" => true,
                "data" => $result->choices[0]->message->content,
            ]);
        }

     }
}