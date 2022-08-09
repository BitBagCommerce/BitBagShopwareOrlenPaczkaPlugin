<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Extension\Order;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class OrlenOrderExtension extends EntityExtension
{
    public const PROPERTY_KEY = 'orlen';

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField(
                'orlen',
                'id',
                'order_id',
                OrlenOrderExtensionDefinition::class,
                true
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return OrderDefinition::class;
    }
}
