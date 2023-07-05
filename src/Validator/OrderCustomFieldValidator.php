<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Validator;

use BitBag\ShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

final class OrderCustomFieldValidator implements OrderCustomFieldValidatorInterface
{
    public function validate(array $data): ConstraintViolationListInterface
    {
        $depthKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_depth';
        $heightKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_height';
        $widthKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_width';
        $packageContentsKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_package_contents';
        $plannedShippingDateKey = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date';

        $data['dimensions'] = 1;
        $dimensions = [$data[$depthKey] ?? null, $data[$heightKey] ?? null, $data[$widthKey] ?? null];
        //var_dump($dimensions);
        $zeros = 0;
        foreach ($dimensions as $dimension) {
            if (1 > $dimension || null === $dimension) {
                ++$zeros;
            }
        }

        if (2 <= $zeros) {
            $data['dimensions'] = 0;
        }

        $constraint = new Assert\Collection([
            'dimensions' => [
                new Assert\NotEqualTo(0, null, 'order.customFields.invalidDimensions'),
            ],
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
            $packageContentsKey => [
                new Assert\NotBlank([
                    'message' => 'order.customFields.packageContentsInvalid',
                ]),
            ],
            $plannedShippingDateKey => [
                new Assert\NotBlank([
                    'message' => 'order.customFields.plannedShippingDateInvalid',
                ]),
            ],
        ]);

        return Validation::createValidator()->validate($data, $constraint);
    }
}
