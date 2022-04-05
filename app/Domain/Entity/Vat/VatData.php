<?php

namespace App\Domain\Entity\Vat;

class VatData
{
    private $countryCode;
    private $vatNumber;
    private $requestDate;
    private $valid;
    private $name;
    private $address;

    /**
     * @return string
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return VatData
     */
    public function setCountryCode(string $countryCode): VatData
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     *
     * @return VatData
     */
    public function setVatNumber(string $vatNumber): VatData
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * @param mixed $requestDate
     *
     * @return VatData
     */
    public function setRequestDate($requestDate): VatData
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function getValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     *
     * @return VatData
     */
    public function setValid(bool $valid): VatData
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return VatData
     */
    public function setName(string $name): VatData
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return VatData
     */
    public function setAddress(string $address): VatData
    {
        $this->address = $address;

        return $this;
    }
}
