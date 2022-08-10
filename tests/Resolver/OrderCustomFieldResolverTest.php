<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Tests\Resolver;

use BitBagShopwareOrlenPaczkaPlugin\Model\OrderCustomFieldModel;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolver;
use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolverInterface;
use BitBagShopwareOrlenPaczkaPlugin\Validator\OrderCustomFieldValidatorInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

final class OrderCustomFieldResolverTest extends TestCase
{
    public function testResolveWithCustomFields(): void
    {
        $orderCustomFieldValidator = $this->createMock(OrderCustomFieldValidatorInterface::class);
        $orderCustomFieldValidator->method('validate')
                                  ->willReturn(new ConstraintViolationList());
        $orderCustomFieldResolver = new OrderCustomFieldResolver($orderCustomFieldValidator);
        $order = $this->getOrderWithCustomFields();
        $orderCustomFieldModel = new OrderCustomFieldModel(
            22,
            11,
            33,
            'package_contents_foo',
            'planned_shipping_date_foo'
        );

        self::assertEquals(
            $orderCustomFieldModel,
            $orderCustomFieldResolver->resolve($order)
        );
    }

    public function testExpectedError(): void
    {
        $this->expectExceptionMessage('bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid');

        $validator = Validation::createValidatorBuilder()->getValidator();

        $constraint = new Length(
            null,
            1,
            null,
            null,
            null,
            null,
            'bitbag.shopware_poczta_polska_app.order.custom_fields.depth_invalid'
        );

        $orderCustomFieldValidator = $this->createMock(OrderCustomFieldValidatorInterface::class);
        $orderCustomFieldValidator->method('validate')
                                  ->willReturn($validator->validate('', $constraint));

        $order = new OrderEntity();
        $order->setCustomFields([]);

        $orderCustomFieldResolver = new OrderCustomFieldResolver($orderCustomFieldValidator);
        $orderCustomFieldResolver->resolve($order);
    }

    private function getOrderWithCustomFields(): OrderEntity
    {
        $order = new OrderEntity();

        $customFields = [
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_depth' => 22,
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_height' => 11,
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_width' => 33,
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_package_contents' => 'package_contents_foo',
            OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date' => 'planned_shipping_date_foo',
        ];

        $order->setCustomFields($customFields);

        return $order;
    }
}
