<?php

namespace Model\Deposit;

use Exception;
use Model\Entity;
use PHPUnit\Framework\TestCase;
use Specifics\ErrorCases\IncorrectParsing;
use Specifics\ErrorCases\NullAttributes;

class DepositImplTest extends TestCase
{


    public function testNullName()
    {
        $this->assertEquals(NullAttributes::CODE, DepositImpl::validateName(''));

    }

    public function testDifferentType()
    {
        $this->assertEquals(IncorrectParsing::CODE, DepositImpl::validateType('anotherType'));
    }
}
