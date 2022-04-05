<?php

/** @noinspection PhpInconsistentReturnPointsInspection */

namespace Illuminate\Contracts\Routing {

    use Symfony\Component\HttpFoundation\Response;

    class ResponseFactory
    {
        public function success($data = null, $headers = []): Response
        {
        }
    }
}

namespace Illuminate\Redis {
    class RedisManager
    {
        public function set($key, $value)
        {
        }

        /**
         * @param $key
         * @param $default
         *
         * @return mixed
         */
        public function get($key, $default)
        {
        }
    }
}
