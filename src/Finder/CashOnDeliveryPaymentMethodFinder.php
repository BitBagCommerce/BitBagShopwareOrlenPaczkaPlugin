<?php declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Finder;

use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\CashPayment;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class CashOnDeliveryPaymentMethodFinder implements CashOnDeliveryPaymentMethodFinderInterface
{
    private EntityRepository $paymentMethodRepository;

    public function __construct(EntityRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function find(Context $context): IdSearchResult
    {
        $criteria = (new Criteria())->addFilter(new EqualsFilter('handlerIdentifier', CashPayment::class));

        return $this->paymentMethodRepository->searchIds($criteria, $context);
    }
}
