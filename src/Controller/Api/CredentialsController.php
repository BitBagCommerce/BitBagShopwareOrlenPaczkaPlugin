<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Controller\Api;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\PPClientResolverInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Validator\FormFieldValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"api"}}))
 */
final class CredentialsController
{
    public const STATUS_UNAUTHORIZED = 'Unauthorized';

    private FormFieldValidator $formFieldValidator;

    private PPClientResolverInterface $clientResolver;

    public function __construct(
        FormFieldValidator $formFieldValidator,
        PPClientResolverInterface $clientResolver
    ) {
        $this->formFieldValidator = $formFieldValidator;
        $this->clientResolver = $clientResolver;
    }

    /**
     * @Route("/api/_action/bitbag-orlen-paczka-plugin/credentials/check", name="api.action.bitbag_orlen_paczka_plugin.credentials.check", methods={"POST"})
     */
    public function check(Request $request): JsonResponse
    {
        $apiConfig = $this->getApiConfigFromRequest($request);
        $client = $this->clientResolver->resolve($apiConfig);

        try {
            // Nothing special but will throw if credentials are wrong
            $client->getOriginOffices();
        } catch (\SoapFault $e) {
            if (self::STATUS_UNAUTHORIZED === $e->getMessage()) {
                throw new InvalidApiConfigException('config.credentialsDataNotValid');
            }
        }

        return new JsonResponse();
    }

    private function getApiConfigFromRequest(Request $request): OrlenApiConfig
    {
        $username = $this->formFieldValidator->validatePresenceOrThrow($request, 'username');
        $password = $this->formFieldValidator->validatePresenceOrThrow($request, 'password');
        $environment = $this->formFieldValidator->validatePresenceOrThrow($request, 'environment');

        return new OrlenApiConfig(
            $username,
            $password,
            $environment
        );
    }
}
