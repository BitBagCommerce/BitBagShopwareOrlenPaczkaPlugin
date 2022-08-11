<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Validator;

use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

final class OrderCustomFieldValidator implements OrderCustomFieldValidatorInterface
{
    public function validate(array $data): ConstraintViolationListInterface
    {
        $depthKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_depth';
        $heightKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_height';
        $widthKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_width';
        $packageContentsKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_package_contents';
        $plannedShippingDateKey = OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date';

        $data[$depthKey] ??= null;
        $data[$heightKey] ??= null;
        $data[$widthKey] ??= null;
        $data[$packageContentsKey] ??= null;
        $data[$plannedShippingDateKey] ??= null;

        $constraint = new Assert\Collection([
            $depthKey => [
                new Assert\NotBlank([
                    'message' => 'order.customFields.depthInvalid',
                ]),
                new Assert\Length([
                    'min' => 1,
                    'minMessage' => 'order.customFields.depthInvalid',
                ]),
                new Assert\NotEqualTo(0, null, 'order.customFields.depthInvalid'),
            ],
            $heightKey => [
                new Assert\NotBlank([
                    'message' => 'order.customFields.heightInvalid',
                ]),
                new Assert\Length([
                    'min' => 1,
                    'minMessage' => 'order.customFields.heightInvalid',
                ]),
                new Assert\NotEqualTo(0, null, 'order.customFields.heightInvalid'),
            ],
            $widthKey => [
                new Assert\NotBlank([
                    'message' => 'order.customFields.widthInvalid',
                ]),
                new Assert\Length([
                    'min' => 1,
                    'minMessage' => 'order.customFields.widthInvalid',
                ]),
                new Assert\NotEqualTo(0, null, 'order.customFields.widthInvalid'),
            ],
            $packageContentsKey => new Assert\NotBlank([
                'message' => 'order.customFields.package_contentsInvalid',
            ]),
            $plannedShippingDateKey => new Assert\NotBlank([
                'message' => 'order.customFields.plannedShippingDateInvalid',
            ]),
        ]);

        return Validation::createValidator()->validate($data, $constraint);
    }
}
