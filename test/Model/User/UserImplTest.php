<?php

namespace Model\User;

use Exception;
use Model\Entity;
use PHPUnit\Framework\TestCase;
use Specifics\ErrorCases\ExceedingMaxLength;
use Specifics\ErrorCases\ExceedingMinLength;
use Specifics\ErrorCases\IncorrectPattern;
use Specifics\ErrorCases\NullAttributes;

class UserImplTest extends TestCase
{
    protected User $validUser;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->validUser = new UserImpl(
            1,
            'john.doe',
            '^@},hJu>[4Bo7TGX',
            'John',
            'Doe',
            '000000000000000000000',
            true
        );
    }

    public function testGetEntityName()
    {
        $this->assertEquals('user', $this->validUser->getEntityName());
    }

    public function testGetAttributesAndVisibility()
    {
        $testedArray = $this->validUser->getAttributesAndVisibility();

        $attributes = [
            'id',
            'username',
            'password',
            'name',
            'surname',
            'IBAN',
            'active'
        ];

        $this->assertEquals(
            $attributes,
            array_keys($testedArray)
        );

        $this->assertEquals(
            4,
            array_count_values($testedArray)[Entity::PUBLIC_VISIBILITY]
        );
    }

    public function testNullUsername()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[username]: ' . NullAttributes::MESSAGE);
        $this->expectExceptionCode(NullAttributes::CODE);

        new UserImpl(
            1,
            '',
            '^@},hJu>[4Bo7TGX',
            'John',
            'Doe',
            '000000000000000000000',
            true
        );
    }

    public function testPasswordTooShort()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[password]: ' . ExceedingMinLength::MESSAGE);
        $this->expectExceptionCode(ExceedingMinLength::CODE);

        new UserImpl(
            1,
            'john.doe',
            '|Ylc|1',
            'John',
            'Doe',
            '000000000000000000000',
            true
        );
    }

    public function testWrongPassword()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[password]: ' . IncorrectPattern::MESSAGE);
        $this->expectExceptionCode(IncorrectPattern::CODE);


        new UserImpl(
            1,
            'john.doe',
            'password',
            'John',
            'Doe',
            '000000000000000000000',
            true
        );
    }

    public function testIBANTooLong()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[IBAN]: ' . ExceedingMaxLength::MESSAGE);
        $this->expectExceptionCode(ExceedingMaxLength::CODE);

        new UserImpl(
            1,
            'john.doe',
            '^@},hJu>[4Bo7TGX',
            'John',
            'Doe',
            '000000000000000000000000000000000',
            true
        );
    }
}
