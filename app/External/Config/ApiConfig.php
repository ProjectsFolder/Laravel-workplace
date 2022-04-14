<?php

namespace App\External\Config;

class ApiConfig
{
    protected $baseUrl;
    protected $apiKey;

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param mixed $baseUrl
     * @return ApiConfig
     */
    public function setBaseUrl($baseUrl): ApiConfig
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     * @return ApiConfig
     */
    public function setApiKey($apiKey): ApiConfig
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
