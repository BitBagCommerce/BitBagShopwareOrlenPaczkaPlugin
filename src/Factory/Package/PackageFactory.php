<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Guid\Guid;
use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\EpoSimple;
use BitBag\PPClient\Model\PackageContent;
use BitBag\PPClient\Model\PaidByEnum;
use BitBag\PPClient\Model\PocztexCourier;
use BitBag\PPClient\Model\RecordedDelivery;
use BitBag\ShopwareOrlenPaczkaPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\OrderExtensionDataResolverInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\PackageSizeResolverInterface;
use DateTime;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class PackageFactory implements PackageFactoryInterface
{
    private OrderWeightCalculatorInterface $orderWeightCalculator;

    private OrderCustomFieldResolverInterface $orderCustomFieldResolver;

    private PackageSizeResolverInterface $packageSizeResolver;

    private PostOfficeFactoryInterface $postOfficeFactory;

    private OrderExtensionDataResolverInterface $orderExtensionDataResolver;

    public function __construct(
        OrderWeightCalculatorInterface $orderWeightCalculator,
        OrderCustomFieldResolverInterface $orderCustomFieldResolver,
        PackageSizeResolverInterface $packageSizeResolver,
        PostOfficeFactoryInterface $postOfficeFactory,
        OrderExtensionDataResolverInterface $orderExtensionDataResolver
    ) {
        $this->orderWeightCalculator = $orderWeightCalculator;
        $this->orderCustomFieldResolver = $orderCustomFieldResolver;
        $this->packageSizeResolver = $packageSizeResolver;
        $this->postOfficeFactory = $postOfficeFactory;
        $this->orderExtensionDataResolver = $orderExtensionDataResolver;
    }

    public function create(
        OrderEntity $order,
        Address $address,
        Context $context
    ): RecordedDelivery {
        $customFields = $this->orderCustomFieldResolver->resolve($order);
        $guid = Guid::generate();
        $weight = $this->orderWeightCalculator->calculate($order, $context);
        $packageSize = $this->packageSizeResolver->resolve(
            $customFields->getDepth(),
            $customFields->getHeight(),
            $customFields->getWidth(),
            (int) round($weight)
        );
        $description = $customFields->getPackageContents();

        $package = new PocztexCourier();
        $package->setGuid($guid);
        $package->setAddress($address);
        $package->setPlannedShippingDate(new DateTime($customFields->getPlannedShippingDate()));
        $package->setWeight((int) ($weight * 1000));
        $package->setPacketGuid($guid);
        $package->setPackagingGuid($guid);
        $package->setDescription($description);
        $package->setEpo(new EpoSimple());
        $package->setPaidBy(PaidByEnum::SENDER);
        $package->setPocztexPackageFormat($packageSize);

        $pickupPointAddress = $this->orderExtensionDataResolver->getPickupPointAddress($order);

        $postOffice = $this->postOfficeFactory->create($pickupPointAddress);

        $package->setDeliveryPackagePoint($postOffice);

        $packageContent = new PackageContent();
        $packageContent->setAnotherPackageContent($description);

        $package->setPackageContents($packageContent);

        return $package;
    }
}
