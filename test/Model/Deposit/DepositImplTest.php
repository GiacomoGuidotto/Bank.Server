<?php

namespace Model\Deposit;

use PHPUnit\Framework\TestCase;
use Specifications\ErrorCases\IncorrectParsing;
use Specifications\ErrorCases\NullAttributes;

class DepositImplTest extends TestCase
{
    public function testNullName()
    {
        $this->assertEquals(
            NullAttributes::CODE,
            DepositImpl::validateName('')
        );
    }

    public function testDifferentType()
    {
        $this->assertEquals(
            IncorrectParsing::CODE,
            DepositImpl::validateType('anotherType')
        );
    }
}
