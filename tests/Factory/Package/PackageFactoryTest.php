<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Tests\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\COD;
use BitBag\PPClient\Model\EpoSimple;
use BitBag\PPClient\Model\PackageContent;
use BitBag\PPClient\Model\PaidByEnum;
use BitBag\PPClient\Model\PaidByReceiver;
use BitBag\PPClient\Model\PaidByReceiverEnum;
use BitBag\PPClient\Model\PocztexCourier;
use BitBag\PPClient\Model\PocztexPackageSizeEnum;
use BitBag\PPClient\Model\PostOffice;
use BitBagShopwareOrlenPaczkaPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBagShopwareOrlenPaczkaPlugin\Factory\Package\PackageFactory;
use BitBagShopwareOrlenPaczkaPlugin\Factory\Package\PackageFactoryInterface;
use BitBagShopwareOrlenPaczkaPlugin\Factory\Package\PostOfficeFactoryInterface;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrderCustomFieldModel;
use BitBagShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolverInterface;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderExtensionDataResolverInterface;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\PackageSizeResolverInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Framework\Context;

final class PackageFactoryTest extends TestCase
{
    private const ORDER_WEIGHT = 5.5;

    private const PLANNED_SHIPPING_DATE = '2022-08-30';

    private const PACKAGE_CONTENTS = 'T-shirt';

    private const TOTAL_AMOUNT = 10.0;

    private const TOTAL_AMOUNT_INT = 1000;

    private const DEPTH = 10;

    private const HEIGHT = 10;

    private const WIDTH = 10;

    public function testCreateWithPaymentPaidInAdvance(): void
    {
        $order = new OrderEntity();
        $order->setAmountTotal(self::TOTAL_AMOUNT);
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

    public function testCreateWithPaymentCashOnDelivery(): void
    {
        $order = new OrderEntity();
        $order->setAmountTotal(self::TOTAL_AMOUNT);
        $order->setCustomFields($this->getCustomFields());

        $paymentMethod = new PaymentMethodEntity();
        $paymentMethod->setHandlerIdentifier(PackageFactoryInterface::CASH_PAYMENT_CLASS);

        $orderTransaction = new OrderTransactionEntity();
        $orderTransaction->setPaymentMethod($paymentMethod);
        $orderTransaction->setUniqueIdentifier('foo');

        $order->setTransactions(new OrderTransactionCollection([$orderTransaction]));

        $context = $this->createMock(Context::class);

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
        $package->setPaidBy(PaidByEnum::RECEIVER);

        $paidByReceiver = new PaidByReceiver();
        $paidByReceiver->setType(PaidByReceiverEnum::INDIVIDUAL_RECEIVER);

        $package->setPaidByReceiver($paidByReceiver);

        $cod = new COD();
        $cod->setTotalAmount(self::TOTAL_AMOUNT_INT);
        $cod->setCodType(COD::COD_TYPE_POSTAL_ORDER);
        $cod->setToBeCheckedByReceiver(false);

        $package->setCod($cod);

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
        $package->setTotalAmount(self::TOTAL_AMOUNT_INT);
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
