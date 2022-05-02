<?php

namespace App\Model\Repository;

use App\Model\DatabaseUtils;
use App\Model\Entity\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository
{
//    public function list(int $perPage = 10): LengthAwarePaginator
//    {
//        return Post::on(DatabaseUtils::DB_REPLICA)->paginate($perPage);
//    }
//
//    public function get(int $id): ?Post
//    {
//        /** @var Post $post */
//        $post = Post::on(DatabaseUtils::DB_REPLICA)->find($id);
//
//        return $post;
//    }
//
//    public function create(string $message): Post
//    {
//
//    }
//
//    public function update(int $id, VatRequest $data): ?Post
//    {
//        /** @var Post $post */
//        $post = Post::on(DatabaseUtils::DB_REPLICA)->find($id);
//        if (empty($post)) {
//            return null;
//        }
//
//        $validated = $data->validated();
//        $post->update($validated);
//
//        return $post;
//    }
//
//    public function delete(int $id): bool
//    {
//        /** @var Post $post */
//        $post = Post::on(DatabaseUtils::DB_REPLICA)->find($id);
//        if (empty($post)) {
//            return false;
//        }
//        $post->delete();
//
//        return true;
//    }
}
