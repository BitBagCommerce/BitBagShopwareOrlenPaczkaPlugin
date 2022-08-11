<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Resolver;

use BitBagShopwareOrlenPaczkaPlugin\Exception\Order\OrderCustomFieldException;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrderCustomFieldModel;
use BitBagShopwareOrlenPaczkaPlugin\Validator\OrderCustomFieldValidatorInterface;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderCustomFieldResolver implements OrderCustomFieldResolverInterface
{
    private OrderCustomFieldValidatorInterface $orderCustomFieldValidator;

    public function __construct(OrderCustomFieldValidatorInterface $orderCustomFieldValidator)
    {
        $this->orderCustomFieldValidator = $orderCustomFieldValidator;
    }

    public function resolve(OrderEntity $order): OrderCustomFieldModel
    {
        $packageDetailsKey = self::PACKAGE_DETAILS_KEY;
        $orderCustomFields = $order->getCustomFields() ?? [];

        $violations = $this->orderCustomFieldValidator->validate($orderCustomFields);
        if (0 !== $violations->count()) {
            throw new OrderCustomFieldException($violations->get(0)->getMessage());
        }

        $depthKey = $packageDetailsKey . '_depth';
        $heightKey = $packageDetailsKey . '_height';
        $widthKey = $packageDetailsKey . '_width';
        $packageContentsKey = $packageDetailsKey . '_package_contents';
        $plannedShippingDate = $packageDetailsKey . '_planned_shipping_date';

        return new OrderCustomFieldModel(
            $orderCustomFields[$depthKey],
            $orderCustomFields[$heightKey],
            $orderCustomFields[$widthKey],
            $orderCustomFields[$packageContentsKey],
            $orderCustomFields[$plannedShippingDate]
        );
    }
}
