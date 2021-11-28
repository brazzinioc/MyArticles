<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function posts()
    {
        return view('posts', [
            'posts' => Post::with('user')->latest()->paginate(10)
        ]);
    }

    public function post($slug)
    {
        $post = Post::where('slug', $slug)->get()->first();

        return view('post', compact('post') );
    }
}
