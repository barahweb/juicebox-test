<?php

namespace App\Services;

use App\Models\Entity\Post;
use App\Models\Entity\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    public function paginate(): LengthAwarePaginator
    {
        return Post::with('user:id,name,email')->latest()->paginate(15);
    }

    public function find(Post $post): Post
    {
        return $post->load('user:id,name,email');
    }

    public function create(User $user, array $data): Post
    {
        $post = $user->posts()->create($data);

        return $post->load('user:id,name,email');
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);

        return $post->load('user:id,name,email');
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }
}
