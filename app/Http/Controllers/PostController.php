<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    private function ensureAdmin(Request $request): void
    {
        $role = strtolower((string) $request->user()?->role);
        $isAdmin = in_array($role, ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);

        abort_unless($isAdmin, 403);
    }

    public function index()
    {
        // Show all posts so employee users can read admin announcements.
        $posts = \App\Models\Post::latest()->get();
        return view('post.index', compact('posts'));
    }

     public function create()
    {
        $this->ensureAdmin(request());

        return view('post.create');
    }


    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }


    public function show(Post $post)
    {
        return view('post.show', compact('post'));
    }


    public function edit(Post $post)
    {
        $this->ensureAdmin(request());

        return view('post.edit', compact('post'));
    }


    public function update(Request $request, Post $post)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }


    public function destroy(Post $post)
    {
        $this->ensureAdmin(request());

        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}