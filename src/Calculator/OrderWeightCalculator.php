<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Calculator;

use BitBag\PPClient\Model\PocztexPackageSizeEnum;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderException;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderWeightCalculatorException;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

final class OrderWeightCalculator implements OrderWeightCalculatorInterface
{
    private EntityRepository $productRepository;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function calculate(OrderEntity $order, Context $context): float
    {
        $totalWeight = 0.0;
        $lineItems = $order->getLineItems();
        if (null === $lineItems) {
            throw new OrderException('order.productsNotFound');
        }

        $lineItems = $lineItems->getElements();

        $products = array_map(static fn (OrderLineItemEntity $item) => $item->getProduct(), $lineItems);
        $products = array_filter($products);
        $parentIds = array_filter($products, static fn (ProductEntity $product) => null !== $product->getParentId());

        $criteria = new Criteria(array_column($parentIds, 'parentId'));
        $searchResult = $this->productRepository->search($criteria, $context);

        $parentProducts = $searchResult->getEntities()->getElements();

        foreach ($lineItems as $item) {
            $product = $item->getProduct();
            $productWeight = 0.0;

            if (null !== $product) {
                $productWeight = $product->getWeight();
                $parentId = $product->getParentId();
                if (null !== $parentId && isset($parentProducts[$parentId])) {
                    /** @var ProductEntity $mainProduct */
                    $mainProduct = $parentProducts[$parentId];

                    $productWeight = $mainProduct->getWeight();
                }
            }

            $totalWeight += $item->getQuantity() * $productWeight;
        }

        if (0.0 === $totalWeight) {
            throw new OrderWeightCalculatorException('order.products.nullWeight');
        }

        if (PocztexPackageSizeEnum::MAX_WEIGHT_2XL <= $totalWeight) {
            throw new OrderWeightCalculatorException('order.products.tooHeavy');
        }

        return $totalWeight;
    }
}
