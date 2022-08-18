<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderCustomFieldException;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrderCustomFieldModel;
use BitBag\ShopwareOrlenPaczkaPlugin\Validator\OrderCustomFieldValidatorInterface;
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
        $packageDetailsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;
        $orderCustomFields = $order->getCustomFields() ?? [];

        $customFields = [
            $packageDetailsKey . '_depth' => $orderCustomFields[$packageDetailsKey . '_depth'] ?? null,
            $packageDetailsKey . '_height' => $orderCustomFields[$packageDetailsKey . '_height'] ?? null,
            $packageDetailsKey . '_width' => $orderCustomFields[$packageDetailsKey . '_width'] ?? null,
            $packageDetailsKey . '_package_contents' => $orderCustomFields[$packageDetailsKey . '_package_contents'] ?? null,
            $packageDetailsKey . '_planned_shipping_date' => $orderCustomFields[$packageDetailsKey . '_planned_shipping_date'] ?? null,
        ];

        $violations = $this->orderCustomFieldValidator->validate($customFields);
        if (0 !== $violations->count()) {
            throw new OrderCustomFieldException((string) $violations->get(0)->getMessage());
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
