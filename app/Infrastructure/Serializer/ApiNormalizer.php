<?php

namespace App\Infrastructure\Serializer;

use App\Utils\DateTimeUtils;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class ApiNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer([
            new ObjectNormalizer(
                new ClassMetadataFactory(
                    new YamlFileLoader(base_path().'/config/serializer.yaml')
                )
            ),
            new DateTimeNormalizer([
                'datetime_format' => DateTimeUtils::APP_DATETIME_FORMAT,
            ]),
        ]);
    }

    public function denormalize($data, $type, $format = null, array $context = []): object
    {
        return $this->serializer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        if ('api' !== $format) {
            return false;
        }

        return $this->serializer->supportsDenormalization($data, $type, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return $this->serializer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if ('api' !== $format) {
            return false;
        }

        return $this->serializer->supportsNormalization($data, $format);
    }
}
