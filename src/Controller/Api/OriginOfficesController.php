<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Controller\Api;

use BitBag\ShopwareOrlenPaczkaPlugin\Config\OrlenApiConfigServiceInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\PPClientResolverInterface;
use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
final class OriginOfficesController
{
    private PPClientResolverInterface $clientResolver;

    private OrlenApiConfigServiceInterface $orlenApiConfigService;

    private SystemConfigService $systemConfigService;

    public function __construct(
        PPClientResolverInterface $clientResolver,
        OrlenApiConfigServiceInterface $orlenApiConfigService,
        SystemConfigService $systemConfigService
    ) {
        $this->orlenApiConfigService = $orlenApiConfigService;
        $this->clientResolver = $clientResolver;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @OA\Get(
     *     path="/api/_action/bitbag-orlen-paczka-plugin/origin-offices",
     *     summary="Gets an Orlen Paczka package label for an order",
     *     operationId="originOffices",
     *     tags={"Admin API", "Orlen Paczka"},
     *     @OA\Parameter(
     *         name="salesChannelId",
     *         description="Identifier of the sales channel",
     *         @OA\Schema(type="string", pattern="^[0-9a-f]{32}$"),
     *         in="query",
     *         required=false
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Render origin offices"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error while rendering origin offices"
     *     )
     * )
     * @Route("/api/_action/bitbag-orlen-paczka-plugin/origin-offices", name="api.action.bitbag_orlen_paczka_plugin.origin_offices", methods={"GET"})
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var string $salesChannelId */
        $salesChannelId = $request->query->get('salesChannelId', '');
        $client = $this->clientResolver->resolve(
            $this->orlenApiConfigService->getApiConfig($salesChannelId)
        );

        try {
            $response = $client->getOriginOffices();
        } catch (\SoapFault $e) {
            if (CredentialsController::STATUS_UNAUTHORIZED === $e->getMessage()) {
                $this->systemConfigService->delete(OrlenApiConfigServiceInterface::SYSTEM_CONFIG_PREFIX . 'originOffice');

                throw new InvalidApiConfigException('config.credentialsDataNotValid');
            }

            throw $e;
        }

        $originOffices = $response->getOriginOffices();
        $jsonResponse = [];

        foreach ($originOffices as $office) {
            $jsonResponse['options'][] = [
                'name' => $office->getDescription(),
                'value' => $office->getId(),
                'id' => (string) $office->getId(),
            ];
        }

        return new JsonResponse($jsonResponse);
    }
}
