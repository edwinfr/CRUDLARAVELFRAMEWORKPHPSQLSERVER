<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index');
    }

 public function list()
{
    return response()->json(
        Post::orderBy('id','desc')->get()
    );
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|max:255',
            'content' => 'nullable',
        ]);

        $post = Post::create($data);

        return response()->json($post);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'   => 'required|max:255',
            'content' => 'nullable',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(['success' => true]);
    }
}
