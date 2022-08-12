<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Core\Checkout\Cart\Validator;

use BitBag\ShopwareOrlenPaczkaPlugin\Core\Checkout\Cart\Custom\Error\InvalidPhoneNumberError;
use BitBag\ShopwareOrlenPaczkaPlugin\Core\Checkout\Cart\Custom\Error\InvalidZipCodeError;
use BitBag\ShopwareOrlenPaczkaPlugin\Core\Checkout\Cart\Custom\Error\StreetSplittingError;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

final class CartValidator implements CartValidatorInterface
{
    public const STREET_FIRST_REGEX = "/(?<streetName>[[:alnum:].'\- ]+)\s+(?<houseNumber>\d{1,10}((\s)?\w{1,3})?(\/\d{1,10})?)$/";

    public const STREET_WITH_BUILDING_NUMBER_REGEX = "/^([^\d]*[^\d\s]) *(\d.*)$/";

    public const ZIP_CODE_REGEX = "/^(\d{2})(-\d{3})?$/i";

    public const PHONE_NUMBER_REGEX = "/(?:(?:\+|00)[0-9]{1,3})?(\d{9,12})/";

    public const PHONE_NUMBER_LENGTH = 9;

    public function validate(
        Cart $cart,
        ErrorCollection $errors,
        SalesChannelContext $context
    ): void {
        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $this->getTechnicalName($context)) {
            return;
        }

        $delivery = $cart->getDeliveries()->first();
        if (null === $delivery) {
            return;
        }

        $address = $delivery->getLocation()->getAddress();
        if (null === $address) {
            return;
        }

        $zipCode = $address->getZipcode();

        $this->checkZipCodeValidity($zipCode, $address->getId(), $errors);

        if (!preg_match(self::STREET_WITH_BUILDING_NUMBER_REGEX, $address->getStreet())) {
            $errors->add(new StreetSplittingError($address->getId()));

            return;
        }

        $phoneNumber = $address->getPhoneNumber();
        if (null === $phoneNumber) {
            $errors->add(new InvalidPhoneNumberError($address->getId()));

            return;
        }

        $phoneNumber = str_replace(['+48', '+', '-', ' '], '', $phoneNumber);

        preg_match(self::PHONE_NUMBER_REGEX, $phoneNumber, $phoneNumberMatches);

        if ([] === $phoneNumberMatches || self::PHONE_NUMBER_LENGTH !== strlen($phoneNumberMatches[0])) {
            $errors->add(new InvalidPhoneNumberError($address->getId()));

            return;
        }

        if ($phoneNumber !== $phoneNumberMatches[0]) {
            $address->setPhoneNumber($phoneNumberMatches[0]);
        }
    }

    private function isZipCodeValid(string $zipCode): bool
    {
        return (bool) preg_match(self::ZIP_CODE_REGEX, $zipCode);
    }

    private function checkZipCodeValidity(
        string $zipCode,
        string $addressId,
        ErrorCollection $errors
    ): void {
        if (!$this->isZipCodeValid($zipCode)) {
            $zipCode = trim(substr_replace($zipCode, '-', 2, 0));
            if (!$this->isZipCodeValid($zipCode)) {
                $errors->add(new InvalidZipCodeError($addressId));
            }
        }
    }

    private function getTechnicalName(SalesChannelContext $context): ?string
    {
        $technicalName = null;
        $shippingMethod = $context->getShippingMethod();
        $shippingMethodCustomFields = $shippingMethod->getCustomFields();

        if (isset($shippingMethodCustomFields['technical_name'])) {
            $technicalName = $shippingMethodCustomFields['technical_name'];
        }

        if (isset($shippingMethod->getTranslated()['customFields']['technical_name'])) {
            $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'];
        }

        return $technicalName;
    }
}
