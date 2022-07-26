<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\EpoSimple;
use BitBag\PPClient\Model\PackageContent;
use BitBag\PPClient\Model\PaidByEnum;
use BitBag\PPClient\Model\PocztexCourier;
use BitBag\PPClient\Model\PocztexPackageSizeEnum;
use BitBag\PPClient\Model\PostOffice;
use BitBag\ShopwareOrlenPaczkaPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package\PackageFactory;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package\PostOfficeFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrderCustomFieldModel;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolverInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\OrderExtensionDataResolverInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\PackageSizeResolverInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class PackageFactoryTest extends TestCase
{
    private const ORDER_WEIGHT = 5.5;

    private const PLANNED_SHIPPING_DATE = '2022-08-30';

    private const PACKAGE_CONTENTS = 'T-shirt';

    private const DEPTH = 10;

    private const HEIGHT = 10;

    private const WIDTH = 10;

    public function testCreateWithPaymentPaidInAdvance(): void
    {
        $order = new OrderEntity();
        $order->setCustomFields($this->getCustomFields());

        $orderWeightCalculator = $this->createMock(OrderWeightCalculatorInterface::class);
        $orderWeightCalculator->method('calculate')->willReturn(self::ORDER_WEIGHT);

        $orderCustomFieldResolver = $this->createMock(OrderCustomFieldResolverInterface::class);
        $orderCustomFieldResolver->method('resolve')->willReturn($this->getCustomFieldsModel());

        $packageSizeResolver = $this->createMock(PackageSizeResolverInterface::class);
        $packageSizeResolver->method('resolve')->willReturn(PocztexPackageSizeEnum::S);

        $postOfficeFactory = $this->createMock(PostOfficeFactoryInterface::class);
        $postOfficeFactory->method('create')->willReturn(new PostOffice());

        $orderExtensionDataResolver = $this->createMock(OrderExtensionDataResolverInterface::class);
        $orderExtensionDataResolver->method('getPickupPointAddress')
                                   ->willReturn($this->getPickupPointAddress());

        $context = $this->createMock(Context::class);

        $packageFactory = new PackageFactory(
            $orderWeightCalculator,
            $orderCustomFieldResolver,
            $packageSizeResolver,
            $postOfficeFactory,
            $orderExtensionDataResolver
        );
        $packageFactory = $packageFactory->create($order, new Address(), $context);

        $package = $this->createPackage(
            $packageFactory->getGuid(),
            $packageFactory->getPacketGuid(),
            $packageFactory->getPackagingGuid()
        );

        self::assertEquals(
            $package,
            $packageFactory
        );
    }

    private function getCustomFields(): array
    {
        return [
            'depth' => self::DEPTH,
            'height' => self::HEIGHT,
            'width' => self::WIDTH,
            'packageContents' => self::PACKAGE_CONTENTS,
            'plannedShippingDate' => self::PLANNED_SHIPPING_DATE,
        ];
    }

    private function getCustomFieldsModel(): OrderCustomFieldModel
    {
        return new OrderCustomFieldModel(
            self::DEPTH,
            self::HEIGHT,
            self::WIDTH,
            self::PACKAGE_CONTENTS,
            self::PLANNED_SHIPPING_DATE
        );
    }

    private function createPackage(
        string $guid,
        string $packetGuid,
        string $packagingGuid
    ): PocztexCourier {
        $package = new PocztexCourier();
        $package->setGuid($guid);
        $package->setAddress(new Address());
        $package->setPlannedShippingDate(new \DateTime(self::PLANNED_SHIPPING_DATE));
        $package->setWeight((int) (self::ORDER_WEIGHT * 1000));
        $package->setPacketGuid($packetGuid);
        $package->setPackagingGuid($packagingGuid);
        $package->setDescription(self::PACKAGE_CONTENTS);
        $package->setEpo(new EpoSimple());
        $package->setPaidBy(PaidByEnum::SENDER);
        $package->setPocztexPackageFormat(PocztexPackageSizeEnum::S);

        $postOffice = new PostOffice();

        $package->setDeliveryPackagePoint($postOffice);

        $packageContent = new PackageContent();
        $packageContent->setAnotherPackageContent(self::PACKAGE_CONTENTS);

        $package->setPackageContents($packageContent);

        return $package;
    }

    private function getPickupPointAddress(): PickupPointAddress
    {
        return new PickupPointAddress(
            '123',
            'ORLEN',
            'Warszawa',
            'ORLEN',
            'Małopolskie',
            'Jasna 4',
            '02-495'
        );
    }
}
