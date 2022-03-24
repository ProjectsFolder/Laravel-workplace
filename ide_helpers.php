<?php

/** @noinspection PhpInconsistentReturnPointsInspection */

namespace Illuminate\Contracts\Routing {

    use Symfony\Component\HttpFoundation\Response;

    class ResponseFactory {
        public function success($data = null): Response { }
    }
}
