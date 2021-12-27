<?php

namespace Model\Deposit;

use PHPUnit\Framework\TestCase;
use Specifications\ErrorCases\IncorrectParsing;

class DepositImplTest extends TestCase
{
    public function testDifferentType()
    {
        $this->assertEquals(
            IncorrectParsing::CODE,
            DepositImpl::validateType('anotherType')
        );
    }
}
