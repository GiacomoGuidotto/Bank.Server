<?php

namespace Model\User;

use PHPUnit\Framework\TestCase;
use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\IncorrectPattern;
use Specifications\ErrorCases\NullAttributes;

class UserImplTest extends TestCase
{
    public function testNullUsername()
    {
        $this->assertEquals(
            NullAttributes::CODE,
            UserImpl::validateUsername('')
        );
    }

    public function testPasswordTooShort()
    {
        $this->assertEquals(
            ExceedingMinLength::CODE,
            UserImpl::validatePassword('|Ylc|1')
        );

    }

    public function testWrongPassword()
    {
        $this->assertEquals(
            IncorrectPattern::CODE,
            UserImpl::validatePassword('password')
        );
    }

    public function testIBANTooLong()
    {
        $this->assertEquals(
            ExceedingMaxLength::CODE,
            UserImpl::validateIBAN('000000000000000000000000000000000')
        );
    }
}
