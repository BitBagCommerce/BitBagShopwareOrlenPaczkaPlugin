<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Model;

final class PickupPointAddress
{
    private string $id;

    private string $pni;

    private string $city;

    private string $name;

    private string $province;

    private string $street;

    private string $postCode;

    public function __construct(
        string $id,
        string $pni,
        string $city,
        string $name,
        string $province,
        string $street,
        string $postCode
    ) {
        $this->id = $id;
        $this->pni = $pni;
        $this->city = $city;
        $this->name = $name;
        $this->province = $province;
        $this->street = $street;
        $this->postCode = $postCode;
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getPni(): int
    {
        return (int) $this->pni;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }
}
