<?php

namespace App\External\Soap;

use App\Domain\Entity\Vat\VatData;
use App\Domain\Interfaces\Output\VatGetterInterface;
use App\Utils\PropertyFiller;
use ReflectionException;
use SoapClient;

class ViesClient implements VatGetterInterface
{
    private $client;

    public function __construct(string $wsdlUrl)
    {
        $this->client = new SoapClient($wsdlUrl);
    }

    /**
     * @param string $vat
     *
     * @return VatData
     *
     * @throws ReflectionException
     */
    public function get(string $vat): ?VatData
    {
        if (!preg_match('/^([A-Z]{2})([0-9]*)$/u', $vat, $matches)) {
            return null;
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $response = $this->client->checkVat([
            'countryCode' => $matches[1],
            'vatNumber' => $matches[2],
        ]);
        $response = json_decode(json_encode($response), true);

        return PropertyFiller::create(VatData::class, $response);
    }
}
