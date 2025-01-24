<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\AI;
use Carbon\Carbon; 
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use App\Services\ReplicateService;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{   

        protected $replicateService;

        public function __construct(ReplicateService $replicateService)
        {
            $this->replicateService = $replicateService;
        }

        // public function createPrediction(Request $request)
        // {
        //     $model = "meta/llama-2-7b-chat";
        //     $version = "f1d50bb24186c52daae319ca8366e53debdaa9e0ae7ff976e918df752732ccc4";

        //     $input = [
        //         'prompt' => $request->input('message')
        //     ];

        //     $result = $this->replicateService->makePrediction($model, $input, $version);

        //     if (isset($result['id'])) {
        //         return response()->json(['prediction_id' => $result['id']]);
        //     }

        //     return response()->json($result, 500); // Return an error if no prediction ID is returned
        // }

        public function createPrediction(Request $request)
        {
            try {
                $userId = auth()->id();

                $model = "meta/llama-2-7b-chat";
                //$model = "meta/llama-2-13b-chat";
                $version = "f1d50bb24186c52daae319ca8366e53debdaa9e0ae7ff976e918df752732ccc4";
                //$version = "6b4da803a2382c08868c5af10a523892f38e2de1aafb2ee55b020d9efef2fdb8";

                $request->validate([
                    "chat_id" => "nullable", 
                ]);

                $input = [
                    'prompt' => $request->input('message'),
                ];

                // Create prediction
                $result = $this->replicateService->makePrediction($model, $input, $version);

                // Save prediction details to the database
                $aiRecord = AI::create([
                    'user_id' => $userId,
                    'message' => $input['prompt'],
                    "chat_id" => $request->chat_id, 
                    'response' => null, // Initially null until the response is available
                    'prediction_id' => $result['id'] ?? null
                ]);

                if (isset($result['id'])) {
                    // Fetch the result immediately after creating the prediction
                    $predictionResult = $this->getPredictionResult($result['id'], $aiRecord->id);

                    return $predictionResult;  // Return the AI response immediately
                }

                // Handle case where no prediction ID is returned
                return response()->json(['error' => 'Prediction creation failed.'], 400);
            //     return response()->json([
            //     'error' => 'Prediction creation failed.',
            //     'details' => [
            //         'response' => $result,
            //         'model' => $model,
            //         'version' => $version,
            //         'input' => $input,
            //     ],
            // ], 400);
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Prediction Error: ' . $e->getMessage()); // Log the actual error message

                // Return a more specific error message
                return response()->json(['error' => 'An error occurred while creating the prediction: ' . $e->getMessage()], 500);
            }
        }

        public function getPredictionResult($predictionId, $aiRecordId)
        {
            $maxRetries = 10; // Increased retry limit
            $retryCount = 0;
            $sleepTime = 10;  // Increased sleep time to allow more time for prediction

            while ($retryCount < $maxRetries) {
                $result = $this->replicateService->getPredictionStatus($predictionId);

                // Log the status of the prediction for debugging
                \Log::info("Prediction Status for ID $predictionId: " . json_encode($result));

                if (!isset($result['status'])) {
                    return response()->json(['error' => 'Invalid prediction response.'], 500);
                }

                if ($result['status'] === 'succeeded') {
                    $output = $this->cleanOutput($result['output'] ?? '');
                    AI::where('id', $aiRecordId)->update(['response' => $output]);
                    \Log::info("Prediction succeeded for ID: $predictionId: " . $output);
                    return response()->json(['output' => $output]);
                }

                if ($result['status'] === 'failed') {
                    \Log::error("Prediction failed for ID: $predictionId - " . ($result['error'] ?? 'Unknown error'));
                    return response()->json(['error' => $result['error'] ?? 'Unknown error'], 500);
                }

                $retryCount++;
                sleep($sleepTime);  // Wait 10 seconds before retrying
            }

            return response()->json(['status' => 'processing']);  // Return "processing" after max retries
        }



        private function cleanOutput($output)
        {
            if (is_array($output)) {
                $output = implode('', $output);
            }

            return trim($output); // Remove any leading/trailing spaces
        }

        public function aiChats(): JsonResponse
        {
            $user = Auth::user();

            // Corrected query condition
            $chats = AI::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->whereNull('chat_id')
                ->whereNotNull('response')
                ->get();

            if ($chats->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your chat history is empty',
                ]);
            }

            return response()->json([
                'status' => true,
                'chats' => $chats
            ]);
        }



        public function aiChat($id): JsonResponse
        {
            $chat = AI::where('id', $id)
                    ->with('responses')
                    ->first();

            if (!$chat) {
                return response()->json([
                    'status' => false,
                    'message' => 'Chat not found',
                ]);
            }

            return response()->json([
                'status' => true,
                'chat' => $chat
            ]);
        }


        // protected $geminiService;

        // public function __construct(GeminiService $geminiService): JsonResponse
        // {
        //     $this->geminiService = $geminiService;
        // }

        // public function ai(Request $request): JsonResponse
        // {
        //     $inputText = $request->input('message');

        //     // Call the Gemini service to generate text
        //     $generatedText = $this->geminiService->generateText($inputText);

        //     // Return a JSON response with the generated text
        //     return response()->json([
        //         "status" => true,
        //         "data" => $generatedText,
        //     ]);
        // }

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