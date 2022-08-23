<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Calculator;

use BitBag\ShopwareOrlenPaczkaPlugin\Calculator\OrderWeightCalculator;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderWeightCalculatorTest extends WebTestCase
{
    public function testCalculate(): void
    {
        $order = new OrderEntity();

        $product = new ProductEntity();
        $product->setWeight(0.8);
        $product->setUniqueIdentifier('foo_product');

        $orderLineItem = new OrderLineItemEntity();
        $orderLineItem->setQuantity(1);
        $orderLineItem->setProduct($product);
        $orderLineItem->setUniqueIdentifier('foo_order_line');

        $product2 = new ProductEntity();
        $product2->setWeight(0.45);
        $product2->setUniqueIdentifier('foo_product_2');

        $orderLineItem2 = new OrderLineItemEntity();
        $orderLineItem2->setQuantity(1);
        $orderLineItem2->setProduct($product2);
        $orderLineItem2->setUniqueIdentifier('foo_order_line_2');

        $order->setLineItems(new OrderLineItemCollection([$orderLineItem, $orderLineItem2]));

        $context = $this->createMock(Context::class);

        $productRepository = $this->createMock(EntityRepository::class);
        $productRepository
            ->method('search')
            ->willReturn(
                new EntitySearchResult(
                    'product',
                    1,
                    new EntityCollection([$product]),
                    new AggregationResultCollection([]),
                    new Criteria(),
                    $context
                )
            );

        $orderWeightCalculator = new OrderWeightCalculator($productRepository);

        self::assertEquals(
            1.25,
            $orderWeightCalculator->calculate($order, $context)
        );
    }

    public function testTooHeavy(): void
    {
        $this->expectExceptionMessage('products.tooHeavy');

        $order = new OrderEntity();

        $product = new ProductEntity();
        $product->setWeight(52.25);
        $product->setUniqueIdentifier('foo');

        $orderLineItem = new OrderLineItemEntity();
        $orderLineItem->setQuantity(1);
        $orderLineItem->setProduct($product);
        $orderLineItem->setUniqueIdentifier('foo');

        $order->setLineItems(new OrderLineItemCollection([$orderLineItem]));

        $context = $this->createMock(Context::class);

        $productRepository = $this->createMock(EntityRepository::class);
        $productRepository
            ->method('search')
            ->willReturn(
                new EntitySearchResult(
                    'product',
                    1,
                    new EntityCollection([$product]),
                    new AggregationResultCollection([]),
                    new Criteria(),
                    $context
                )
            );

        $orderWeightCalculator = new OrderWeightCalculator($productRepository);

        self::assertEquals(
            52.25,
            $orderWeightCalculator->calculate($order, $context)
        );
    }
}
