<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Tests\Validator;

use BitBagShopwareOrlenPaczkaPlugin\Resolver\OrderCustomFieldResolverInterface;
use BitBagShopwareOrlenPaczkaPlugin\Validator\OrderCustomFieldValidator;
use PHPUnit\Framework\TestCase;

final class OrderCustomFieldValidatorTest extends TestCase
{
    /** @dataProvider provideData */
    public function testValidator(string $key, string $value): void
    {
        $orderCustomFieldValidator = new OrderCustomFieldValidator();
        self::assertEquals(
            4,
            $orderCustomFieldValidator->validate([$key => $value])->count()
        );
    }

    public function provideData(): array
    {
        return [
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_depth', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_height', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_width', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_package_contents', 'foo'],
           [OrderCustomFieldResolverInterface::PACKAGE_DETAILS_KEY . '_planned_shipping_date', 'foo'],
        ];
    }
}
