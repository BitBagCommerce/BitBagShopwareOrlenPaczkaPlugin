<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Extension\Order;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class OrlenOrderExtensionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bitbag_orlen_order_extension';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('pickup_point_pni', 'pickupPointPni', 10))->addFlags(new Required()),
            (new StringField('pickup_point_city', 'pickupPointCity'))->addFlags(new Required()),
            (new StringField('pickup_point_name', 'pickupPointName'))->addFlags(new Required()),
            (new StringField('pickup_point_province', 'pickupPointProvince'))->addFlags(new Required()),
            (new StringField('pickup_point_street', 'pickupPointStreet'))->addFlags(new Required()),
            (new StringField('pickup_point_zip_code', 'pickupPointZipCode'))->addFlags(new Required()),
            new StringField('package_id', 'packageId'),
            new FkField('order_id', 'orderId', OrderDefinition::class),
            new OneToOneAssociationField('order', 'order_id', 'id', OrderDefinition::class, false),
        ]);
    }
}
