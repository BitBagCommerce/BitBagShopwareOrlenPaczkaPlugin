<?php declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Controller\Api;

use BitBag\PPClient\Model\OriginOffice;
use BitBag\ShopwareOrlenPaczkaPlugin\Config\OrlenApiConfigServiceInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\PPClientResolverInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Validator\FormFieldValidator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
final class OriginOfficesController
{
    private FormFieldValidator $formFieldValidator;

    private PPClientResolverInterface $clientResolver;

    private OrlenApiConfigServiceInterface $orlenApiConfigService;

    public function __construct(
        FormFieldValidator $formFieldValidator,
        PPClientResolverInterface $clientResolver,
        OrlenApiConfigServiceInterface $orlenApiConfigService
    )
    {
        $this->formFieldValidator = $formFieldValidator;
        $this->orlenApiConfigService = $orlenApiConfigService;
        $this->clientResolver = $clientResolver;
    }

    /**
     * @Route("/api/_action/bitbag-orlen-paczka-plugin/origin-offices", name="api.action.bitbag_orlen_paczka_plugin.origin_offices", methods={"POST"})
     */
    public function __invoke(Request $request): Response
    {
        $salesChannelId = $this->formFieldValidator->validatePresenceOrThrow($request, 'salesChannelId', true);
        $client = $this->clientResolver->resolve(
            $this->orlenApiConfigService->getApiConfig($salesChannelId)
        );

        $response = $client->getOriginOffices();
        $originOffices = $response->getOriginOffices();

//        $jsonResponse = array_map(
//            fn (OriginOffice $o) => [$o->getId() => $o->getName()],
//            $originOffices
//        );

        $jsonResponse['placeholder'] = ['en-GB' => 'Origin office', 'de-DE' => 'Scheisse', 'pl-PL' => 'Urząd nadania'];
        $jsonResponse['label'] = ['en-GB' => 'Origin office', 'de-DE' => 'Scheisse', 'pl-PL' => 'Urząd nadania'];

        foreach ($originOffices as $office) {
            $jsonResponse['options'][] = [
                'label' => [
                    'en-GB' => $office->getDescription(),
                    'de-DE' => $office->getDescription(),
                    'pl-PL' => $office->getDescription(),
                ],

                'value' => $office->getId(),
            ];

        }

        return new JsonResponse($jsonResponse);
    }
}
