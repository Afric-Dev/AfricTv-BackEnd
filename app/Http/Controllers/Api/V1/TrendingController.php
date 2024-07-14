<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Post;

class TrendingController extends Controller
{
    public function trending() {
        $posts = Post::all();

        foreach($posts as $post) {
            
        }
    }
}
 