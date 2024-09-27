<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class AIController extends Controller
{
    public function Ai(Request $request)
    {

        $request->validate([
            "message" => "required|max:255",
        ]);

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
