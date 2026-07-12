<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagResource;
use App\Http\Responses\ApiResponse;
use App\Models\Tag;

class TagController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $query = Tag::query();

        $query = $this->applySearch($query, ['name']);
        $query = $this->applySort($query);

        $tags = $query->paginate($this->perPage());

        return $this->paginated($tags, TagResource::class);
    }

    public function postsBySlug(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $query = $tag->posts()
            ->with(['author', 'category', 'tags'])
            ->published();

        $query = $this->applySearch($query, ['title', 'content', 'excerpt']);
        $query = $this->applySort($query);

        $posts = $query->paginate($this->perPage());

        return $this->paginated($posts, PostResource::class);
    }
}
