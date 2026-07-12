<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Models\Post;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $query = Category::withCount('posts');

        $query = $this->applySearch($query, ['name', 'description']);
        $query = $this->applySort($query);

        $categories = $query->paginate($this->perPage());

        return $this->paginated($categories, CategoryResource::class);
    }

    public function postsBySlug(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = Post::with(['author', 'category', 'tags'])
            ->where('category_id', $category->id)
            ->published();

        $query = $this->applySearch($query, ['title', 'content', 'excerpt']);
        $query = $this->applySort($query);

        $posts = $query->paginate($this->perPage());

        return $this->paginated($posts, PostResource::class);
    }
}
