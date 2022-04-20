<?php

/** @noinspection PhpInconsistentReturnPointsInspection */

namespace Illuminate\Contracts\Routing {

    use Illuminate\Http\Response;

    class ResponseFactory
    {
        public function success($data = null, $meta = [], $headers = []): Response
        {
        }

        public function attachment($content, $filename, $headers = []): Response
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
