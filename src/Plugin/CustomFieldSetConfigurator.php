<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Plugin;

use BitBagShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBagShopwareOrlenPaczkaPlugin\Finder\PackageDetailsCustomFieldSetFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

final class CustomFieldSetConfigurator implements CustomFieldSetConfiguratorInterface
{
    private PackageDetailsCustomFieldSetFinderInterface $packageDetailsCustomFieldSetFinder;

    private CustomFieldsForPackageDetailsPayloadFactoryInterface $customFieldsForPackageDetailsPayloadFactory;

    private EntityRepositoryInterface $customFieldSetRepository;

    public function __construct(
        PackageDetailsCustomFieldSetFinderInterface $packageDetailsCustomFieldSetFinder,
        CustomFieldsForPackageDetailsPayloadFactoryInterface $customFieldsForPackageDetailsPayloadFactory,
        EntityRepositoryInterface $customFieldSetRepository
    ) {
        $this->packageDetailsCustomFieldSetFinder = $packageDetailsCustomFieldSetFinder;
        $this->customFieldsForPackageDetailsPayloadFactory = $customFieldsForPackageDetailsPayloadFactory;
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    public function createCustomFieldSetForPackageDetails(Context $context): void
    {
        $customFields = $this->packageDetailsCustomFieldSetFinder->search($context);

        if (0 !== $customFields->getTotal()) {
            return;
        }

        $data = $this->customFieldsForPackageDetailsPayloadFactory->create();

        $this->customFieldSetRepository->upsert([$data], $context);
    }
}
