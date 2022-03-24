<?php

namespace App\Service;

use App\Utils\PropertyFiller;
use App\Model\Dto\VatData;
use App\Service\Interfaces\VatGetterInterface;
use Exception;
use ReflectionException;
use SoapClient;

class VIES implements VatGetterInterface
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
    public function get(string $vat): VatData
    {
        if (!preg_match('/^([A-Z]{2})([0-9]*)$/u', $vat, $matches)) {
            throw new Exception('Invalid VatData format');
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
