<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Model;

final class OrlenApiConfig
{
    private string $username;

    private string $password;

    private string $environment;

    private ?int $originOffice;

    public function __construct(
        string $username,
        string $password,
        string $environment,
        ?int $originOffice = null
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->environment = $environment;
        $this->originOffice = $originOffice;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getOriginOffice(): ?int
    {
        return $this->originOffice;
    }
}
