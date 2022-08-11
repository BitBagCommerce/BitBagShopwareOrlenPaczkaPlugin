<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBagShopwareOrlenPaczkaPlugin\Exception\Order\OrderAddressException;
use BitBagShopwareOrlenPaczkaPlugin\Service\StreetSplitterInterface;
use BitBagShopwareOrlenPaczkaPlugin\Validator\IsPhoneNumber;
use BitBagShopwareOrlenPaczkaPlugin\Validator\IsPostalCode;
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

        $postalCode = $orderAddress->zipcode;
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
