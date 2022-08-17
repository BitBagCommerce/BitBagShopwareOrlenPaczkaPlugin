<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Controller;

use BitBag\ShopwareOrlenPaczkaPlugin\Api\PackageApiServiceInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\PackageException;
use BitBag\ShopwareOrlenPaczkaPlugin\Extension\Order\OrlenOrderExtension;
use BitBag\ShopwareOrlenPaczkaPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\OrderExtensionDataResolverInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Service\OrlenConfigServiceInterface;
use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
final class CreatePackageController
{
    private EntityRepository $orderRepository;

    private OrderFinderInterface $orderFinder;

    private PackageApiServiceInterface $packageApiService;

    private OrderExtensionDataResolverInterface $orderExtensionDataResolver;

    private OrlenConfigServiceInterface $orlenConfigService;

    public function __construct(
        EntityRepository $orderRepository,
        OrderFinderInterface $orderFinder,
        PackageApiServiceInterface $packageApiService,
        OrderExtensionDataResolverInterface $orderExtensionDataResolver,
        OrlenConfigServiceInterface $orlenConfigService
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFinder = $orderFinder;
        $this->packageApiService = $packageApiService;
        $this->orderExtensionDataResolver = $orderExtensionDataResolver;
        $this->orlenConfigService = $orlenConfigService;
    }

    /**
     * @OA\Post(
     *     path="/api/_action/bitbag-orlen-paczka-plugin/package/{orderId}",
     *     summary="Creates an Orlen Paczka package for an order",
     *     operationId="create",
     *     tags={"Admin API", "Orlen Paczka"},
     *     @OA\Parameter(
     *         name="orderId",
     *         description="Identifier of the order the package should be generated for",
     *         @OA\Schema(type="string", pattern="^[0-9a-f]{32}$"),
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Package created successfully.",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad package data provided"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     )
     * )
     * @Route("/api/_action/bitbag-orlen-paczka-plugin/package/{orderId}", name="api.action.bitbag_orlen_paczka_plugin.package", methods={"POST"}})
     */
    public function create(string $orderId, Context $context): JsonResponse
    {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);

        $orderExtensionData = $this->orderExtensionDataResolver->getData($order);
        if (null !== $orderExtensionData['packageId']) {
            throw new PackageException('package.alreadyCreated');
        }

        $config = $this->orlenConfigService->getApiConfig();
        $package = $this->packageApiService->createPackage($config, $order, $context);

        $this->orderRepository->update([
            [
                'id' => $order->getId(),
                OrlenOrderExtension::PROPERTY_KEY => [
                    'id' => $orderExtensionData['id'],
                    'packageId' => $package->getGuid(),
                ],
            ],
        ], $context);

        return new JsonResponse($order, Response::HTTP_CREATED);
    }
}
