<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin;

if (file_exists(\dirname(__DIR__) . '/vendor/autoload.php')) {
    $loader = require \dirname(__DIR__) . '/vendor/autoload.php';
    if (true !== $loader) {
        spl_autoload_unregister([$loader, 'loadClass']);
        $loader->register(false);
    }
}

use BitBag\ShopwareOrlenPaczkaPlugin\Config\OrlenApiConfigServiceInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Extension\Order\OrlenOrderExtensionDefinition;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\CustomFieldsForPackageDetailsPayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Plugin\CustomFieldSetConfiguratorInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Plugin\RuleConfiguratorInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Plugin\ShippingMethodConfiguratorInterface;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

final class BitBagShopwareOrlenPaczkaPlugin extends Plugin
{
    private CustomFieldSetConfiguratorInterface $customFieldSetConfigurator;

    private RuleConfiguratorInterface $ruleConfigurator;

    private ShippingMethodConfiguratorInterface $shippingMethodConfigurator;

    private Connection $connection;

    public function setCustomFieldSetConfigurator(CustomFieldSetConfiguratorInterface $customFieldSetConfigurator): void
    {
        $this->customFieldSetConfigurator = $customFieldSetConfigurator;
    }

    public function setRuleConfigurator(RuleConfiguratorInterface $ruleConfigurator): void
    {
        $this->ruleConfigurator = $ruleConfigurator;
    }

    public function setShippingMethodConfigurator(ShippingMethodConfiguratorInterface $shippingMethodConfigurator): void
    {
        $this->shippingMethodConfigurator = $shippingMethodConfigurator;
    }

    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }

    public function activate(ActivateContext $activateContext): void
    {
        $context = $activateContext->getContext();

        $ruleId = $this->ruleConfigurator->getRuleId($context);

        $this->shippingMethodConfigurator->createShippingMethod($ruleId, $context);
        $this->shippingMethodConfigurator->toggleActiveShippingMethod(true, $context);
        $this->customFieldSetConfigurator->createCustomFieldSetForPackageDetails($context);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->shippingMethodConfigurator->toggleActiveShippingMethod(false, $deactivateContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $db = $this->connection;

        $db->executeStatement('DROP TABLE IF EXISTS `' . OrlenOrderExtensionDefinition::ENTITY_NAME . '`;');

        $db->executeStatement(
            'DELETE FROM system_config
            WHERE configuration_key LIKE :domain',
            [
                'domain' => OrlenApiConfigServiceInterface::SYSTEM_CONFIG_PREFIX . '.%',
            ],
        );

        $db->executeStatement(
            'DELETE FROM custom_field_set where JSON_EXTRACT(config, "$.technical_name") = :technicalName',
            [
                'technicalName' => CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY,
            ],
        );

        $db->executeStatement(
            'DELETE FROM snippet WHERE translation_key LIKE :translationKey',
            [
                'translationKey' => '%customFields.' . CustomFieldsForPackageDetailsPayloadFactoryInterface::PACKAGE_DETAILS_KEY . '%',
            ],
        );

        $this->shippingMethodConfigurator->toggleActiveShippingMethod(false, $uninstallContext->getContext());
    }
}
