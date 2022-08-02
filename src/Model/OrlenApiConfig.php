<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Model;

final class OrlenApiConfig
{
    private string $username;

    private string $password;

    private string $environment;

    public function __construct(
        string $username,
        string $password,
        string $environment
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->environment = $environment;
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
}
