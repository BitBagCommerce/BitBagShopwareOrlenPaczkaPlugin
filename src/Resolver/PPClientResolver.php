<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\PPClient\Client\PPClient;
use BitBag\PPClient\Client\PPClientConfiguration;
use BitBag\PPClient\Factory\Client\SoapClientFactory;
use BitBag\PPClient\Factory\Response\AddDeliveryResponseFactory;
use BitBag\PPClient\Factory\Response\ClearEnvelopeResponseFactory;
use BitBag\PPClient\Factory\Response\GetLabelResponseFactory;
use BitBag\PPClient\Factory\Response\GetOriginOfficeResponseFactory;
use BitBag\PPClient\Factory\Response\SendEnvelopeResponseFactory;
use BitBag\PPClient\Normalizer\ArrayNormalizer;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

final class PPClientResolver implements PPClientResolverInterface
{
    private const PRODUCTION_ENVIRONMENT = 'production';

    public function resolve(OrlenApiConfig $config): PPClient
    {
        $arrayNormalizer = new ArrayNormalizer();
        $soapClientFactory = new SoapClientFactory();

        $wsdlFile = self::PRODUCTION_ENVIRONMENT === $config->getEnvironment() ?
            'client_prod.wsdl' :
            'client_dev.wsdl';
        $ppClientConfiguration = new PPClientConfiguration(
            __DIR__ . "/../../vendor/bitbag/pp-client/src/Resources/$wsdlFile",
            $config->getUsername(),
            $config->getPassword(),
        );

        return new PPClient(
            $soapClientFactory->create($ppClientConfiguration),
            new AddDeliveryResponseFactory(),
            new ClearEnvelopeResponseFactory($arrayNormalizer),
            new GetLabelResponseFactory($arrayNormalizer),
            new SendEnvelopeResponseFactory($arrayNormalizer),
            new GetOriginOfficeResponseFactory()
        );
    }
}
