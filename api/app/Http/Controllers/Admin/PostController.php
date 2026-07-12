<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Responses\ApiResponse;
use App\Models\Post;

class PostController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $query = Post::with(['author', 'category', 'tags']);

        $query = $this->applySearch($query, ['title', 'content', 'excerpt']);
        $query = $this->applySort($query);
        $query = $this->applyFilters($query);

        $posts = $query->latest()->paginate($this->perPage());

        return $this->paginated($posts, PostResource::class);
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create($request->validated());

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return new PostResource($post->load(['author', 'category', 'tags']));
    }

    public function show(Post $post)
    {
        $post->load(['author', 'category', 'tags', 'featuredImage']);

        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return new PostResource($post->load(['author', 'category', 'tags']));
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }
}
