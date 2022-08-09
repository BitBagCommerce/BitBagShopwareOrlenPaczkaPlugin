<?php declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\EventSubscriber;

use BitBagShopwareOrlenPaczkaPlugin\Exception\InvalidZipCodeException;
use BitBagShopwareOrlenPaczkaPlugin\Exception\MissingFormFieldException;
use BitBagShopwareOrlenPaczkaPlugin\Extension\Order\OrlenOrderExtension;
use BitBagShopwareOrlenPaczkaPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodTranslation\ShippingMethodTranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CartConvertedSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private EntityRepository $shippingMethodTranslationRepository;

    public function __construct(
        RequestStack $requestStack,
        EntityRepository $shippingMethodTranslationRepository
    ) {
        $this->requestStack = $requestStack;
        $this->shippingMethodTranslationRepository = $shippingMethodTranslationRepository;
    }


    public static function getSubscribedEvents(): array
    {
        return [
            CartConvertedEvent::class => 'onCartConverted',
        ];
    }

    public function onCartConverted(CartConvertedEvent $event): void
    {
        $orderData = $event->getConvertedCart();

        $criteria = (new Criteria())->addFilter(new EqualsFilter('customFields.technical_name', ShippingMethodPayloadFactoryInterface::SHIPPING_KEY));

        $shippingMethodTranslations = $this->shippingMethodTranslationRepository->search($criteria, $event->getContext());
        if (0 === $shippingMethodTranslations->count()) {
            return;
        }

        /** @var ShippingMethodTranslationEntity|null $shippingMethodTranslation */
        $shippingMethodTranslation = $shippingMethodTranslations->first();



        $delivery = $orderData['deliveries'][0];

        if ($delivery['shippingMethodId'] !== $shippingMethodTranslation->getShippingMethodId()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $pni = $this->validatePresenceOrThrow($request, 'orlenPickupPointPni');
        $city = $this->validatePresenceOrThrow($request, 'orlenPickupPointCity');
        $name = $this->validatePresenceOrThrow($request, 'orlenPickupPointName');
        $province = $this->validatePresenceOrThrow($request, 'orlenPickupPointProvince');
        $street = $this->validatePresenceOrThrow($request, 'orlenPickupPointStreet');
        $zipCode = $this->validatePresenceOrThrow($request, 'orlenPickupPointZipCode');


        $orderZipCode = $delivery['shippingOrderAddress']['zipcode'] ?? null;
        $validatedZipCode = $this->validateZipCodeOrThrow($orderZipCode);
        $delivery['shippingOrderAddress']['zipcode'] = trim(substr_replace($validatedZipCode, '-', 2, 0));

        $orderData['deliveries'][0] = $delivery;
        $orderData['extensions'][OrlenOrderExtension::PROPERTY_KEY] = [
            'id' => Uuid::randomHex(),
            'pickupPointPni' => $pni,
            'pickupPointCity' => $city,
            'pickupPointName' => $name,
            'pickupPointProvince' => $province,
            'pickupPointStreet' => $street,
            'pickupPointZipCode' => $zipCode,
        ];

        $event->setConvertedCart($orderData);
    }

    private function validateZipCodeOrThrow(string $zipCode): string
    {
        $matches = [];
        \preg_match('(\d{5})', $zipCode, $matches);

        $validatedZipCode = $matches[1] ?? null;

        if (5 !== \strlen($validatedZipCode) || null === $validatedZipCode) {
            throw new InvalidZipCodeException($validatedZipCode);
        }

        return $validatedZipCode;
    }
}
