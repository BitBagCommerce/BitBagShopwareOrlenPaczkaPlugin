<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Factory\Package;

use BitBag\PPClient\Model\Address;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package\AddressFactory;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\StreetSplitterInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddressFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $streetSplitter = $this->createMock(StreetSplitterInterface::class);
        $streetSplitter
            ->method('splitStreet')
            ->willReturn(['Jasna 4/5', 'Jasna', '4/5']);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $orderAddressEntity = new OrderAddressEntity();
        $orderAddressEntity->setFirstName('Jan');
        $orderAddressEntity->setLastName('Kowalski');
        $orderAddressEntity->setStreet('Jasna 4/5');
        $orderAddressEntity->setCity('Warszawa');
        $orderAddressEntity->setZipcode('02-495');
        $orderAddressEntity->setPhoneNumber('500-000-000');

        $address = new Address();
        $address->setName('Jan Kowalski');
        $address->setStreet('Jasna');
        $address->setHouseNumber('4');
        $address->setFlatNumber('5');
        $address->setCity('Warszawa');
        $address->setPostCode('02-495');
        $address->setMobileNumber('500-000-000');
        $address->setEmail('email@test.com');

        $addressFactory = new AddressFactory(
            $streetSplitter,
            $validator
        );

        $this->assertEquals(
            $address,
            $addressFactory->create($orderAddressEntity, 'email@test.com')
        );
    }
}
