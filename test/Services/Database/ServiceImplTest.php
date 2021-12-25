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
    protected string $validUsername = 'giacomo.guidotto';
    protected string $validPassword = 'Fr6/ese342f';
    protected string $validToken;

    /**
     * Utility method
     * generate a random string for attribute testing
     *
     * @param int $length the optional string length
     * @return string the generated string
     */
    private function generateString(int $length = 8): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function setUp(): void
    {
        $this->service = new ServiceImpl();

        $this->validToken = $this->service->authenticate(
            $this->validUsername,
            $this->validPassword
        )['token'];
    }

    // ==== Authenticate =======================================================

    public function testTokenGeneration()
    {
        $testedToken = $this->service->authenticate(
            $this->validUsername,
            $this->validPassword
        );

        var_dump($testedToken);

        $this->validToken = $testedToken['token'];

        self::assertEquals(
            Success::CODE,
            SessionImpl::validateToken($testedToken['token'])
        );

    }

    // ==== Create a new user ==================================================

    public function testUserCreation()
    {
        $generatedUsername = $this->generateString(12);
        $generatedName = $this->generateString();
        $generatedSurname = $this->generateString();

        $testedArray = $this->service->createUser(
            $generatedUsername,
            $this->validPassword,
            $generatedName,
            $generatedSurname,
        );

        var_dump($testedArray);

        self::assertEquals(
            $generatedUsername,
            $testedArray['username']
        );
        self::assertEquals(
            $generatedName,
            $testedArray['name']
        );
        self::assertEquals(
            $generatedSurname,
            $testedArray['surname']
        );
        self::assertEquals(
            Success::CODE,
            UserImpl::validateIBAN($testedArray['IBAN'])
        );

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

    // ==== Get the user information ===========================================

    public function testUserInformation()
    {
        $testedArray = $this->service->getUser(
            $this->validToken
        );

        var_dump($testedArray);

        self::assertEquals(
            Success::CODE,
            UserImpl::validateUsername($testedArray['username'])
        );
        self::assertEquals(
            Success::CODE,
            UserImpl::validateName($testedArray['name'])
        );
        self::assertEquals(
            Success::CODE,
            UserImpl::validateSurname($testedArray['surname'])
        );
        self::assertEquals(
            Success::CODE,
            UserImpl::validateIBAN($testedArray['IBAN'])
        );
    }

    // ==== Close a specific user ==============================================

    public function testCloseUser()
    {
        $dummyToken = $this->service->authenticate(
            'LSkfOBdyPVfF',
            $this->validPassword
        );

        var_dump($dummyToken);

        $testedArray = $this->service->closeUser(
            $dummyToken['token']
        );

        var_dump($testedArray);

        self::assertNull($testedArray);
    }

    // ==== Close a specific session ===========================================

    public function testCloseSession()
    {
        $testedArray = $this->service->closeSession(
            $this->validToken
        );

        var_dump($testedArray);

        self::assertNull($testedArray);
    }
}
