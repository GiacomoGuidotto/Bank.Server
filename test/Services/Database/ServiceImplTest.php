<?php

namespace Services\Database;

use Model\Session\SessionImpl;
use Model\User\UserImpl;
use PHPUnit\Framework\TestCase;
use Specifications\ErrorCases\AlreadyExist;
use Specifications\ErrorCases\Success;

class ServiceImplTest extends TestCase
{
    protected ServiceImpl $service;

    private function generateString(
        int    $length = 8,
        string $prefix = '',
        string $suffix = '',
        bool   $withDigits = false
    ): string
    {
        $characters = ($withDigits ? '0123456789' : '') . 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $prefix . $randomString . $suffix;
    }

    protected function setUp(): void
    {
        $this->service = new ServiceImpl();
    }

    // ==== Authenticate =======================================================

    public function testTokenGeneration()
    {
        $testedToken = $this->service->authenticate(
            'giacomo.guidotto',
            'Fr6/ese342f'
        );

        var_dump($testedToken);
        
        self::assertEquals(
            Success::CODE,
            SessionImpl::validateToken($testedToken['token'])
        );

    }

    // ==== Create a new user ==================================================

    public function testUserCreation()
    {
        $generatedUsername = $this->generateString();

        $testedArray = $this->service->createUser(
            $generatedUsername,
            'Fr6/ese342f',
            'Giacomo',
            'Guidotto',
        );

        self::assertEquals(
            $generatedUsername,
            $testedArray['username']
        );
        self::assertEquals(
            'Giacomo',
            $testedArray['name']
        );
        self::assertEquals(
            'Guidotto',
            $testedArray['surname']
        );
        self::assertEquals(
            Success::CODE,
            UserImpl::validateIBAN($testedArray['IBAN'])
        );

        var_dump($testedArray);
    }

    public function testUsernameAlreadyExist()
    {
        $testedArray = $this->service->createUser(
            'giacomo.guidotto',
            'Fr6/ese342f',
            'Giacomo',
            'Guidotto',
        );

        self::assertEquals(
            AlreadyExist::CODE,
            $testedArray['error']
        );
        self::assertEquals(
            AlreadyExist::MESSAGE,
            $testedArray['message']
        );
        self::assertEquals(
            AlreadyExist::DETAILS,
            $testedArray['details']
        );

        var_dump($testedArray);
    }
}
