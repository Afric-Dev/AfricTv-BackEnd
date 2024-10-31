<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback as FeedbackModel;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function feedback(Request $request): JsonResponse
    {
        $request->validate([
            "feature_love" => "required",
            "recommend" => "required",
            "user_email" => "required",
        ]);

        // Create a new instance of the Feedback model and save it to the database
        $feedback = FeedbackModel::create([
            "feature_love" => $request->feature_love,
            "recommend" => $request->recommend,
            "user_email" => $request->user_email,
        ]);

        // You can send an email or perform other actions here if needed

        return response()->json([
            "status" => true,
            "message" => "Feedback Sent Successfully"
        ]);
    }
}
