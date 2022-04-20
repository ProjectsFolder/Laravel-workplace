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

namespace Illuminate\Contracts\Cache {

    use Illuminate\Cache\TaggedCache;

    class Repository
    {
        /**
         * Get a lock instance.
         *
         * @param  string  $name
         * @param  int  $seconds
         * @param  string|null  $owner
         * @return Lock
         */
        public function lock($name, $seconds = 0, $owner = null)
        {

        }

        /**
         * Restore a lock instance using the owner identifier.
         *
         * @param  string  $name
         * @param  string  $owner
         * @return Lock
         */
        public function restoreLock($name, $owner)
        {

        }

        /**
         * Begin executing a new tags operation.
         *
         * @param  array|mixed  $names
         * @return TaggedCache
         */
        public function tags($names)
        {

        }
    }
}
