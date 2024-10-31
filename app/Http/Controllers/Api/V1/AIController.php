<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\AI;
use Carbon\Carbon;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;

class AIController extends Controller
{

        protected $geminiService;

        public function __construct(GeminiService $geminiService): JsonResponse
        {
            $this->geminiService = $geminiService;
        }

        public function ai(Request $request): JsonResponse
        {
            $inputText = $request->input('message');

            // Call the Gemini service to generate text
            $generatedText = $this->geminiService->generateText($inputText);

            // Return a JSON response with the generated text
            return response()->json([
                "status" => true,
                "data" => $generatedText,
            ]);
        }

        // protected $falconService;

        // public function __construct(FalconService $falconService)
        // {
        //     $this->falconService = $falconService;
        // }

        // public function AI(Request $request)
        // {
        //     $inputText = $request->input('message');

        //     // Call the Falcon service to generate text
        //     $generatedText = $this->falconService->generateText($inputText);

        //     // Return a JSON response with the generated text
        //     return response()->json([
        //         "status" => true,
        //         "data" => $generatedText,
        //     ]);
        // }


        // protected $bloomService;

        // public function __construct(BloomService $bloomService)
        // {
        //     $this->bloomService = $bloomService;
        // }

        // public function AI(Request $request)
        // {
        //     $inputText = $request->input('message');

        //     $generatedText = $this->bloomService->generateText($inputText);

        //     return response()->json([
        //            "status" => true,
        //            "data" => $generatedText,
        //     ]);

        // }

    // public function AI(Request $request)
    // {

    //     $request->validate([
    //             "message" => "required|max:255",
    //     ]);
    //     $userId = auth()->id(); 

    //     // Retrieve the user's count in the last 24 hours
    //     $userCount = AI::where('user_id', $userId)
    //         ->where('created_at', '>=', Carbon::now()->subDay()) // 24 hours
    //         ->first();

    //     // If user count exists and is 10 or more
    //     if ($userCount && $userCount->count >= 10) {
    //         return response()->json(['message' => 'You have reached your daily limit'], 403);
    //     }

    //     // If count is less than 10, perform the action
    //     if ($userCount) {
    //         $userCount->count += 1; // Increment the count
    //         $userCount->save();
    //         //Perfrom AI action
    //         $result = OpenAI::chat()->create([
    //             'model' => 'gpt-3.5-turbo',
    //             'messages' => [
    //                 ['role' => 'user', 'content' => $request->message],
    //             ],
    //         ]);
    //         return response->json([
    //             "status" => true,
    //             "data" => $result->choices[0]->message->content,
    //         ]);

    //     } else {
    //         // If no record exists for the last 24 hours, create a new one
    //         AI::create([
    //             'user_id' => $userId,
    //             'count' => 1,
    //         ]);
    //         //Perfrom AI action
    //         $result = OpenAI::chat()->create([
    //             'model' => 'gpt-3.5-turbo',
    //             'messages' => [
    //                 ['role' => 'user', 'content' => $request->message],
    //             ],
    //         ]);

    //         return response->json([
    //             "status" => true,
    //             "data" => $result->choices[0]->message->content,
    //         ]);
    //     }

    //  }
}