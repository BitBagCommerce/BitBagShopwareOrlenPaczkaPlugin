<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\PPClient\Model\RecordedDelivery;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

interface PackageFactoryInterface
{
    public const CASH_PAYMENT_CLASS = 'Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment';

    public function create(
        OrderEntity $order,
        Address $address,
        Context $context
    ): RecordedDelivery;
}
