<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Guid\Guid;
use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\COD;
use BitBag\PPClient\Model\EpoSimple;
use BitBag\PPClient\Model\PackageContent;
use BitBag\PPClient\Model\PaidByEnum;
use BitBag\PPClient\Model\PaidByReceiver;
use BitBag\PPClient\Model\PaidByReceiverEnum;
use BitBag\PPClient\Model\PocztexCourier;
use BitBag\PPClient\Model\RecordedDelivery;
use BitBagShopwareOrlenPaczkaPlugin\Calculator\OrderWeightCalculatorInterface;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolverInterface;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderExtensionDataResolverInterface;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\PackageSizeResolverInterface;
use DateTime;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
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
        $totalAmount = (int) ($order->getAmountTotal() * 100);

        $package = new PocztexCourier();
        $package->setGuid($guid);
        $package->setAddress($address);
        $package->setPlannedShippingDate(new DateTime($customFields->getPlannedShippingDate()));
        $package->setWeight((int) ($weight * 1000));
        $package->setTotalAmount($totalAmount);
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

        $paymentMethod = null;

        $transactions = $order->getTransactions();

        if (null !== $transactions) {
            $firstTransaction = $transactions->first();
            if (null !== $firstTransaction) {
                $paymentMethod = $firstTransaction->getPaymentMethod();
            }
        }

        if ($this->isCashOnDelivery($paymentMethod)) {
            $package->setPaidBy(PaidByEnum::RECEIVER);

            $paidByReceiver = new PaidByReceiver();
            $paidByReceiver->setType(PaidByReceiverEnum::INDIVIDUAL_RECEIVER);

            $package->setPaidByReceiver($paidByReceiver);

            $cod = new COD();
            $cod->setTotalAmount($totalAmount);
            $cod->setCodType(COD::COD_TYPE_POSTAL_ORDER);
            $cod->setToBeCheckedByReceiver(false);

            $package->setCod($cod);
        }

        return $package;
    }

    private function isCashOnDelivery(?PaymentMethodEntity $paymentMethod): bool
    {
        if (null === $paymentMethod) {
            return false;
        }

        $orderPaymentMethodHandlerIdentifier = $paymentMethod->getHandlerIdentifier();

        return self::CASH_PAYMENT_CLASS === $orderPaymentMethodHandlerIdentifier;
    }
}
