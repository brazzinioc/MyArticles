<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\ { Auth, Storage };

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->get();
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $post = New Post;
        return view('posts.create', compact('post') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        // save data
        $post = Post::create( [
            'user_id' => Auth::id(),
            'title' => $request->input('titulo'),
            'body' => $request->input('contenido'),
            'iframe' => $request->input('iframe'),
        ]);

        // save image
        if($request->file('imagen')) {
            $post->image = $request->file('imagen')->store('posts', 'public'); //save on /storage/app/posts
            $post->save();
        }

        //return response
        return back()->with('status', 'Creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    /*
    public function show(Post $post)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        //update row
        $post->user_id = Auth::id();
        $post->title = $request->input('titulo');
        $post->body = $request->input('contenido');
        $post->iframe = $request->input('iframe');
        $post->save();

        //update image
        if($request->file('imagen')) {

            //Delete current image
            Storage::disk('public')->delete($post->image);

            //Save new image
            $post->image = $request->file('imagen')->store('posts', 'public'); //save on /storage/app/public/posts
            $post->save();
        }

        return back()->with('status', 'Actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        Storage::disk('public')->delete($post->image);
        $post->delete();

        return back()->with('status', 'Eliminado con éxito');
    }
}
