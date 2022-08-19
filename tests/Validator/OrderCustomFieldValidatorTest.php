<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Validator;

use BitBag\ShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Validator\OrderCustomFieldValidator;
use PHPUnit\Framework\TestCase;

final class OrderCustomFieldValidatorTest extends TestCase
{
    /** @dataProvider provideData */
    public function testValidator(string $key, string $value): void
    {
        $orderCustomFieldValidator = new OrderCustomFieldValidator();

        self::assertEquals(
            5,
            $orderCustomFieldValidator->validate([$key => $value])->count()
        );
    }

    public function provideData(): array
    {
        return [
            [CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_depth', '11'],
            [CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_height', '12'],
            [CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_width', '13'],
            [CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_package_contents', 'foo'],
            [CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date', 'foo'],
        ];
    }
}
