<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\PPClient\Client\PPClient;
use BitBag\PPClient\Client\PPClientConfiguration;
use BitBag\PPClient\Factory\Client\SoapClientFactory;
use BitBag\PPClient\Factory\Response\AddDeliveryResponseFactory;
use BitBag\PPClient\Factory\Response\ClearEnvelopeResponseFactory;
use BitBag\PPClient\Factory\Response\GetLabelResponseFactory;
use BitBag\PPClient\Factory\Response\GetOriginOfficeResponseFactory;
use BitBag\PPClient\Factory\Response\SendEnvelopeResponseFactory;
use BitBag\PPClient\Normalizer\ArrayNormalizer;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

final class ApiResolver implements ApiResolverInterface
{
    public function getClient(OrlenApiConfig $config): PPClient
    {
        $arrayNormalizer = new ArrayNormalizer();
        $soapClientFactory = new SoapClientFactory();
        $wsdlFile = PPClientResolver::PRODUCTION_ENVIRONMENT === $config->getEnvironment() ?
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
