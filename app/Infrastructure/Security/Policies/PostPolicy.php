<?php

namespace App\Infrastructure\Security\Policies;

use App\Model\Entity\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any posts.
     *
     * @param  $user
     * @return mixed
     */
    public function viewAny($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param  $user
     * @param  Post  $post
     * @return mixed
     */
    public function view($user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  $user
     * @return mixed
     */
    public function create($user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param  $user
     * @param  Post  $post
     * @return mixed
     */
    public function update($user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  $user
     * @param  Post  $post
     * @return mixed
     */
    public function delete($user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
