<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Http\Responses\ApiResponse;
use App\Models\Page;

class PageController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $query = Page::with('author')->published();

        $query = $this->applySearch($query, ['title', 'content', 'excerpt']);
        $query = $this->applySort($query);
        $query = $this->applyFilters($query);

        $pages = $query->paginate($this->perPage());

        return $this->paginated($pages, PageResource::class);
    }

    public function show(string $slug)
    {
        $page = Page::with(['author', 'featuredImage'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return new PageResource($page);
    }
}
