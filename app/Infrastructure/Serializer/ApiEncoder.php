<?php

namespace App\Infrastructure\Serializer;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class ApiEncoder implements EncoderInterface, DecoderInterface
{
    public function decode(string $data, string $format, array $context = [])
    {
        return json_decode($data);
    }

    public function supportsDecoding(string $format): bool
    {
        return 'api' == $format;
    }

    public function encode($data, string $format, array $context = []): string
    {
        if (isset($data['meta'])) {
            $context = array_merge($context, $data['meta']);
            unset($data['meta']);
        }
        $result['success'] = true;
        if ($context['success'] ?? false) {
            $result['data'] = is_array($data) && 1 == count($data) ? array_shift($data) : $data;
            unset($context['success']);
            unset($context['groups']);
            $result['meta'] = $context;
        } else {
            $result['success'] = false;
            if (isset($context['error_message'])) {
                $result['message'] = $context['error_message'];
            }
            if (isset($context['error_code'])) {
                $result['code'] = $context['error_code'];
            }
//            if (isset($context['error_traceback'])) {
//                $result['traceback'] = $context['error_traceback'];
//            }
            if (isset($context['error_file'])) {
                $result['file'] = $context['error_file'];
            }
            if (isset($context['error_line'])) {
                $result['line'] = $context['error_line'];
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function supportsEncoding(string $format): bool
    {
        return 'api' == $format;
    }
}
