<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Model\Entity\Post;
use App\Model\Repository\PostRepository;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostController extends Controller
{
    protected $postRepository;
    protected $translator;

    public function __construct(PostRepository $postRepository, Translator $translator)
    {
        $this->postRepository = $postRepository;
        $this->translator = $translator;
    }

    public function list(): Response
    {
        $this->authorize('viewAny', Post::class);
        $list = $this->postRepository->list();

        return response()->success($list);
    }

    public function get(int $id): Response
    {
        $post = $this->getPost($id);
        $this->authorize('view', $post);

        return response()->success($post);
    }

    public function create(PostRequest $request): Response
    {
        $this->authorize('create', Post::class);
        $post = $this->postRepository->create($request);

        return response()->success($post);
    }

    public function update(int $id, PostRequest $request): Response
    {
        $post = $this->getPost($id);
        $this->authorize('update', $post);
        $post = $this->postRepository->update($post, $request);

        return response()->success($post);
    }

    public function delete(int $id): Response
    {
        $post = $this->getPost($id);
        $this->authorize('delete', $post);
        $this->postRepository->delete($post);

        return response()->success();
    }

    private function getPost(int $id): Post
    {
        $post = $this->postRepository->get($id);
        if (empty($post)) {
            throw new HttpException(404, $this->translator->get('messages.not_found'));
        }

        return $post;
    }
}
