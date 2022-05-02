<?php

namespace App\Model\Repository;

use App\Http\Requests\PostRequest;
use App\Infrastructure\Storage\FileStorageInterface;
use App\Model\DatabaseUtils;
use App\Model\Entity\Post;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository
{
    protected $guard;
    protected $fileStorage;

    public function __construct(Guard $guard, FileStorageInterface $fileStorage)
    {
        $this->guard = $guard;
        $this->fileStorage = $fileStorage;
    }

    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Post::on(DatabaseUtils::DB_REPLICA)->paginate($perPage);
    }

    public function get(int $id): ?Post
    {
        /** @var Post $post */
        $post = Post::on(DatabaseUtils::DB_REPLICA)->find($id);

        return $post;
    }

    public function create(PostRequest $data): Post
    {
        $validated = $data->validated();
        $validated['user_id'] = $this->guard->id();
        /** @var Post $post */
        $post = Post::create($validated);
        if (!empty($file = $data->file('attachment'))) {
            $this->fileStorage->store($file->get(), $file->getClientOriginalName(), "post$post->id");
            $update['attachment'] = $file->getClientOriginalName();
            $post->update($update);
        }

        return $post;
    }

    public function update($post, PostRequest $data): ?Post
    {
        if (!($post instanceof Post)) {
            /** @var Post $post */
            $post = Post::query()->find($post);
            if (empty($post)) {
                return null;
            }
        }
        $update = $data->validated();
        if (!empty($file = $data->file('attachment')) || !empty($data->get('delete_attachment'))) {
            $this->fileStorage->deleteArea("post$post->id");
            $update['attachment'] = null;
            if (!empty($file)) {
                $this->fileStorage->store($file->get(), $file->getClientOriginalName(), "post$post->id");
                $update['attachment'] = $file->getClientOriginalName();
            }
        }
        unset($update['delete_attachment']);

        $post->setConnection(DatabaseUtils::DB_MASTER)->update($update);

        return $post;
    }

    public function delete($post): bool
    {
        if (!($post instanceof Post)) {
            /** @var Post $post */
            $post = Post::query()->find($post);
            if (empty($post)) {
                return false;
            }
        }
        $this->fileStorage->deleteArea("post$post->id");
        $post->setConnection(DatabaseUtils::DB_MASTER)->delete();

        return true;
    }
}
