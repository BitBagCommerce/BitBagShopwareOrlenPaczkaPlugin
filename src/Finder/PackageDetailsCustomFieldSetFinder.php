<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Finder;

use BitBagShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class PackageDetailsCustomFieldSetFinder implements PackageDetailsCustomFieldSetFinderInterface
{
    private EntityRepository $customFieldSetRepository;

    public function __construct(EntityRepository $customFieldSetRepository)
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function search(Context $context): IdSearchResult
    {
        $customFieldsCriteria = (new Criteria())
            ->addFilter(
                new EqualsFilter(
                    CustomFieldsForPackageDetailsPayloadFactoryInterface::TECHNICAL_NAME,
                    CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY
                )
            );

        return $this->customFieldSetRepository->searchIds($customFieldsCriteria, $context);
    }
}
