<?php

namespace Model\Loan;

use PHPUnit\Framework\TestCase;
use Specifics\ErrorCases\ExceedingMaxRange;
use Specifics\ErrorCases\IncorrectParsing;
use Specifics\ErrorCases\IncorrectPattern;
use Specifics\ErrorCases\NullAttributes;

class LoanImplTest extends TestCase
{
    public function testNullName()
    {
        $this->assertEquals(
            NullAttributes::CODE,
            LoanImpl::validateName('')
        );
    }

    public function testIncorrectInterestRate()
    {
        $this->assertEquals(
            ExceedingMaxRange::CODE,
            LoanImpl::validateInterestRate(10)
        );
    }

    public function testIncorrectRepaymentDay()
    {
        $this->assertEquals(
            IncorrectPattern::CODE,
            LoanImpl::validateRepaymentDay('2021/12/25 12:00:00')
        );
    }

    public function testDifferentType()
    {
        $this->assertEquals(
            IncorrectParsing::CODE,
            LoanImpl::validateType('anotherType')
        );
    }
}
