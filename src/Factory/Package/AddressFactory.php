<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderAddressException;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\StreetSplitterInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Validator\IsPhoneNumber;
use BitBag\ShopwareOrlenPaczkaPlugin\Validator\IsPostalCode;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddressFactory implements AddressFactoryInterface
{
    private StreetSplitterInterface $streetSplitter;

    private ValidatorInterface $validator;

    public function __construct(StreetSplitterInterface $streetSplitter, ValidatorInterface $validator)
    {
        $this->streetSplitter = $streetSplitter;
        $this->validator = $validator;
    }

    public function create(OrderAddressEntity $orderAddress, string $email): Address
    {
        $addressStreet = str_replace(['  ', ' / '], ['', '/'], $orderAddress->getStreet());

        $flatNumber = null;
        [, $street, $houseNumber] = $this->streetSplitter->splitStreet($addressStreet);

        if (str_contains($houseNumber, '/')) {
            $explodedHouseNumber = explode('/', $houseNumber);

            [$houseNumber, $flatNumber] = $explodedHouseNumber;
        }

        $phoneNumber = $orderAddress->getPhoneNumber() ?? '';
        $this->throwOnConstraintViolations($phoneNumber, new IsPhoneNumber());

        $postalCode = $orderAddress->getZipcode();
        $this->throwOnConstraintViolations($postalCode, new IsPostalCode());

        $address = new Address();
        $address->setName($orderAddress->getFirstName() . ' ' . $orderAddress->getLastName());
        $address->setEmail($email);
        $address->setCity($orderAddress->getCity());
        $address->setPostCode($postalCode);
        $address->setStreet($street);
        $address->setFlatNumber(trim($flatNumber ?? ''));
        $address->setHouseNumber(trim($houseNumber));
        $address->setMobileNumber($phoneNumber);

        return $address;
    }

    private function throwOnConstraintViolations(string $value, Constraint $constraint): void
    {
        $violationList = $this->validator->validate($value, $constraint);
        if (0 !== $violationList->count()) {
            throw new OrderAddressException((string) $violationList->get(0)->getMessage());
        }
    }
}
