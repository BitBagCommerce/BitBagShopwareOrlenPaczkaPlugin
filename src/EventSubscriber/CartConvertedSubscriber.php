<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\EventSubscriber;

use BitBag\ShopwareOrlenPaczkaPlugin\Core\Checkout\Cart\Validator\CartValidator;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\NoRequestException;
use BitBag\ShopwareOrlenPaczkaPlugin\Extension\Order\OrlenOrderExtension;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Validator\FormFieldValidatorInterface;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodTranslation\ShippingMethodTranslationEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class CartConvertedSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private EntityRepository $shippingMethodTranslationRepository;

    private FormFieldValidatorInterface $formFieldValidator;

    public function __construct(
        RequestStack $requestStack,
        EntityRepository $shippingMethodTranslationRepository,
        FormFieldValidatorInterface $formFieldValidator
    ) {
        $this->requestStack = $requestStack;
        $this->shippingMethodTranslationRepository = $shippingMethodTranslationRepository;
        $this->formFieldValidator = $formFieldValidator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CartConvertedEvent::class => 'onCartConverted',
        ];
    }

    public function onCartConverted(CartConvertedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new NoRequestException();
        }

        $orderData = $event->getConvertedCart();

        $criteria = (new Criteria())->addFilter(new EqualsFilter('customFields.technical_name', ShippingMethodPayloadFactoryInterface::SHIPPING_KEY));

        $shippingMethodTranslations = $this->shippingMethodTranslationRepository->search($criteria, $event->getContext());
        if (0 === $shippingMethodTranslations->count()) {
            return;
        }

        /** @var ShippingMethodTranslationEntity|null $shippingMethodTranslation */
        $shippingMethodTranslation = $shippingMethodTranslations->first();
        if (null === $shippingMethodTranslation) {
            return;
        }

        $delivery = $orderData['deliveries'][0];
        if ($delivery['shippingMethodId'] !== $shippingMethodTranslation->getShippingMethodId()) {
            return;
        }

        $pni = $this->formFieldValidator->validatePresenceOrThrow($request, 'orlenPickupPointPni');
        $city = $this->formFieldValidator->validatePresenceOrThrow($request, 'orlenPickupPointCity');
        $name = $this->formFieldValidator->validatePresenceOrThrow($request, 'orlenPickupPointName');
        $province = $this->formFieldValidator->validatePresenceOrThrow($request, 'orlenPickupPointProvince');
        $street = $this->formFieldValidator->validatePresenceOrThrow($request, 'orlenPickupPointStreet');
        $zipCode = $this->formFieldValidator->validatePresenceOrThrow($request, 'orlenPickupPointZipCode');

        $deliveryZipCode = $delivery['shippingOrderAddress']['zipcode'];
        if (!$this->isZipCodeValid($deliveryZipCode)) {
            $delivery['shippingOrderAddress']['zipcode'] = trim(substr_replace($deliveryZipCode, '-', 2, 0));

            $orderData['deliveries'][0] = $delivery;
        }

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

    private function isZipCodeValid(string $zipCode): bool
    {
        return (bool) preg_match(CartValidator::ZIP_CODE_REGEX, $zipCode);
    }
}
