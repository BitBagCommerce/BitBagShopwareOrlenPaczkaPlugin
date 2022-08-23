<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Finder;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderNotFoundException;
use BitBag\ShopwareOrlenPaczkaPlugin\Extension\Order\OrlenOrderExtension;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

final class OrderFinder implements OrderFinderInterface
{
    private EntityRepository $orderRepository;

    public function __construct(EntityRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getWithAssociations(string $orderId, Context $context): OrderEntity
    {
        $orderCriteria = new Criteria([$orderId]);
        $orderCriteria->addAssociations([
            'deliveries',
            'lineItems',
            'lineItems.product',
            'deliveries.shippingMethod',
            'addresses',
            'transactions',
            'transactions.paymentMethod',
            OrlenOrderExtension::PROPERTY_KEY,
            'salesChannel',
            'documents',
            'documents.documentMediaFile',
        ]);

        $order = $this->orderRepository->search($orderCriteria, $context)->first();
        if (null === $order) {
            throw new OrderNotFoundException('order.notFound');
        }

        return $order;
    }
}
