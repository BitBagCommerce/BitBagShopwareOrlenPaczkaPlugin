<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Controller\Api;

use BitBagShopwareOrlenPaczkaPlugin\Config\OrlenApiConfigServiceInterface;
use BitBagShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\PPClientResolverInterface;
use BitBagShopwareOrlenPaczkaPlugin\Validator\FormFieldValidator;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
final class CredentialsController
{
    private const STATUS_UNAUTHORIZED = 'Unauthorized';

    private FormFieldValidator $formFieldValidator;

    private PPClientResolverInterface $clientResolver;

    public function __construct(
        FormFieldValidator $formFieldValidator,
        OrlenApiConfigServiceInterface $orlenApiConfigService,
        PPClientResolverInterface $clientResolver
    ) {
        $this->formFieldValidator = $formFieldValidator;
        $this->orlenApiConfigService = $orlenApiConfigService;
        $this->clientResolver = $clientResolver;
    }

    /**
     * @Route("/api/_action/bitbag-orlen-paczka-plugin/credentials/check", name="api.action.bitbag_orlen_paczka_plugin.credentials.check", methods={"POST"})
     */
    public function check(Request $request): JsonResponse
    {
        $apiConfig = $this->getApiConfigFromRequest($request);
//        $client = $this->clientResolver->resolve($apiConfig);

//        try {
//            // Nothing special but will throw if credentials are wrong
//            $client->getOriginOffices();
//        } catch (\SoapFault $e) {
//            if (self::STATUS_UNAUTHORIZED === $e->getMessage()) {
//                throw new InvalidApiConfigException();
//            }
//        }

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

//        return new OrlenApiConfig(
//            'jakub.groncki@bitbag.pl',
//            '',
//            'sandbox'
//        );
    }
}