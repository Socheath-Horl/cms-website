<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Http\Responses\ApiResponse;
use App\Models\Page;

class PageController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $query = Page::with('author');

        $query = $this->applySearch($query, ['title', 'content', 'excerpt']);
        $query = $this->applySort($query);
        $query = $this->applyFilters($query);

        $pages = $query->latest()->paginate($this->perPage());

        return $this->paginated($pages, PageResource::class);
    }

    public function store(StorePageRequest $request)
    {
        $page = Page::create($request->validated());

        return new PageResource($page->load('author'));
    }

    public function show(Page $page)
    {
        $page->load(['author', 'featuredImage']);

        return new PageResource($page);
    }

    public function update(UpdatePageRequest $request, Page $page)
    {
        $page->update($request->validated());

        return new PageResource($page->load('author'));
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return response()->noContent();
    }
}
