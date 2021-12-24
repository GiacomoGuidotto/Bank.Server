<?php

namespace Model\Transaction;

use PHPUnit\Framework\TestCase;
use Specifications\ErrorCases\IncorrectParsing;
use Specifications\ErrorCases\IncorrectPattern;
use Specifications\ErrorCases\NullAttributes;

class TransactionImplTest extends TestCase
{
    public function testDifferentType()
    {
        $this->assertEquals(
            IncorrectParsing::CODE,
            TransactionImpl::validateType('anotherType')
        );
    }

    public function testIncorrectTimestamp()
    {
        $this->assertEquals(
            IncorrectPattern::CODE,
            TransactionImpl::validateTimestamp('2021/12/25 12:00:00')
        );
    }

    public function testNullAuthor()
    {
        $this->assertEquals(
            NullAttributes::CODE,
            TransactionImpl::validateAuthor('')
        );
    }
}
