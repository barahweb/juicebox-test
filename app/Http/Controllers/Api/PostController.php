<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Entity\Post;
use App\Services\PostService;
use Illuminate\Http\Response;

class PostController extends ApiController
{
    public function __construct(private PostService $postService)
    {
    }

    public function index()
    {
        return $this->sendResponse($this->postService->paginate(), 'Daftar post berhasil diambil.');
    }

    public function show(Post $post)
    {
        return $this->sendResponse($this->postService->find($post), 'Detail post berhasil diambil.');
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->create($request->user(), $request->validated());

        return $this->sendResponse($post, 'Post berhasil dibuat.', Response::HTTP_CREATED);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post = $this->postService->update($post, $request->validated());

        return $this->sendResponse($post, 'Post berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $this->postService->delete($post);

        return $this->sendResponse(null, 'Post ID ' . $post->id . ' berhasil dihapus.');
    }
}
