<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Factory;

use BitBag\ShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactory;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldsForPackageDetailsPayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new CustomFieldsForPackageDetailsPayloadFactory();

        self::assertEquals(
            $this->getCustomFieldsData(),
            $factory->create()
        );
    }

    private function getCustomFieldsData(): array
    {
        $customFieldPrefix = CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY;

        return [
            'name' => $customFieldPrefix,
            'config' => [
                'label' => [
                    'en-GB' => 'Package details (Orlen Package)',
                    'pl-PL' => 'Szczegóły paczki (Orlen Paczka)',
                ],
                'translated' => true,
                'technical_name' => $customFieldPrefix,
            ],
            'customFields' => [
                [
                    'name' => $customFieldPrefix . '_height',
                    'label' => 'Height (cm)',
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Height (cm)',
                            'pl-PL' => 'Wysokość (cm)',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_width',
                    'label' => 'Width (cm)',
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Width (cm)',
                            'pl-PL' => 'Szerokość (cm)',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_depth',
                    'label' => 'Depth (cm)',
                    'type' => CustomFieldTypes::INT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Depth (cm)',
                            'pl-PL' => 'Głębokość (cm)',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_package_contents',
                    'label' => 'Package content',
                    'type' => CustomFieldTypes::TEXT,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Package content',
                            'pl-PL' => 'Zawartość paczki',
                        ],
                    ],
                ],
                [
                    'name' => $customFieldPrefix . '_planned_shipping_date',
                    'label' => 'Shipping date',
                    'type' => CustomFieldTypes::DATETIME,
                    'config' => [
                        'label' => [
                            'en-GB' => 'Shipping date',
                            'pl-PL' => 'Data nadania',
                        ],
                    ],
                ],
            ],
            'relations' => [[
                'entityName' => 'order',
            ]],
        ];
    }
}
