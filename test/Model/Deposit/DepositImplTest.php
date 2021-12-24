<?php

namespace Model\Deposit;

use Exception;
use Model\Entity;
use PHPUnit\Framework\TestCase;
use Specifics\ErrorCases\IncorrectParsing;
use Specifics\ErrorCases\NullAttributes;

class DepositImplTest extends TestCase
{
    protected Deposit $validDeposit;

    /**
     * @throws Exception
     */
    public function testCorrectInstance()
    {
        $testedUser = new DepositImpl(
            1,
            1,
            'Deposit1',
            22000,
            'standard',
            true
        );

        $this->assertInstanceOf(Deposit::class, $testedUser);

        $this->validDeposit = $testedUser;
    }


    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->validDeposit = new DepositImpl(
            1,
            1,
            'Deposit1',
            22000,
            'standard',
            true
        );
    }

    public function testGetEntityName()
    {
        $this->assertEquals('deposit', $this->validDeposit->getEntityName());

    }

    public function testGetAttributesAndVisibility()
    {
        $testedArray = $this->validDeposit->getAttributesAndVisibility();

        $attributes = [
            'id',
            'userId',
            'name',
            'amount',
            'type',
            'active'
        ];

        $this->assertEquals(
            $attributes,
            array_keys($testedArray)
        );

        $this->assertEquals(
            3,
            array_count_values($testedArray)[Entity::PUBLIC_VISIBILITY]
        );
    }

    public function testNullName()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[name]: ' . NullAttributes::MESSAGE);
        $this->expectExceptionCode(NullAttributes::CODE);

        new DepositImpl(
            1,
            1,
            '',
            22000,
            'standard',
            true
        );
    }

    public function testDifferentType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[type]: ' . IncorrectParsing::MESSAGE);
        $this->expectExceptionCode(IncorrectParsing::CODE);

        new DepositImpl(
            1,
            1,
            'Deposit1',
            22000,
            'anotherType',
            true
        );
    }
}
