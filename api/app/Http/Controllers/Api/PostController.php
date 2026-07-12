<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Responses\ApiResponse;
use App\Models\Post;

class PostController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $query = Post::with(['author', 'category', 'tags'])->published();

        $query = $this->applySearch($query, ['title', 'content', 'excerpt']);
        $query = $this->applySort($query);
        $query = $this->applyFilters($query);

        $posts = $query->paginate($this->perPage());

        return $this->paginated($posts, PostResource::class);
    }

    public function show(string $slug)
    {
        $post = Post::with(['author', 'category', 'tags', 'featuredImage'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new PostResource($post);
    }
}
