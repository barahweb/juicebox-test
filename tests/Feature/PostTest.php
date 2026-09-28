<?php

use App\Models\Entity\Post;
use App\Models\Entity\User;
use Laravel\Sanctum\Sanctum;

it('bisa lihat daftar post', function () {
    Sanctum::actingAs(User::factory()->create());
    Post::factory()->count(3)->create();

    $response = $this->getJson('/api/posts');

    $response->assertOk()
        ->assertJson(['success' => true])
        ->assertJsonCount(3, 'data.data');
});

it('bisa lihat detail post', function () {
    Sanctum::actingAs(User::factory()->create());
    $post = Post::factory()->create();

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $post->id)
        ->assertJsonPath('data.title', $post->title);
});

it('dapat 404 kalau post tidak ditemukan', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->getJson('/api/posts/999999');

    $response->assertNotFound()
        ->assertJson(['success' => false]);
});

it('bisa bikin post baru', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/posts', [
        'title' => 'Judul Post',
        'content' => 'Isi post',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Judul Post')
        ->assertJsonPath('data.user_id', $user->id);

    $this->assertDatabaseHas('posts', [
        'title' => 'Judul Post',
        'user_id' => $user->id,
    ]);
});

it('gagal bikin post kalau title kosong', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/posts', ['content' => 'Isi tanpa judul']);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

it('tidak bisa bikin post tanpa login', function () {
    $response = $this->postJson('/api/posts', [
        'title' => 'Judul',
        'content' => 'Isi',
    ]);

    $response->assertUnauthorized();
});

it('pemilik post bisa update post miliknya', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $post = Post::factory()->for($user)->create();

    $response = $this->patchJson("/api/posts/{$post->id}", ['title' => 'Judul Baru']);

    $response->assertOk()->assertJsonPath('data.title', 'Judul Baru');

    $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Judul Baru']);
});

it('user lain tidak bisa update post orang lain', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create(['title' => 'Judul Asli']);

    Sanctum::actingAs($other);

    $response = $this->patchJson("/api/posts/{$post->id}", ['title' => 'Coba Rebut']);

    $response->assertForbidden();
    $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Judul Asli']);
});

it('pemilik post bisa hapus post miliknya', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $post = Post::factory()->for($user)->create();

    $response = $this->deleteJson("/api/posts/{$post->id}");

    $response->assertOk()->assertJson(['success' => true]);
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

it('user lain tidak bisa hapus post orang lain', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    Sanctum::actingAs($other);

    $response = $this->deleteJson("/api/posts/{$post->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});
