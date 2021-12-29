<?php

namespace Services\Database;

use Model\Deposit\DepositImpl;
use Model\Session\SessionImpl;
use Model\Transaction\TransactionImpl;
use Model\User\UserImpl;
use PHPUnit\Framework\TestCase;
use Specifications\ErrorCases\AlreadyExist;
use Specifications\ErrorCases\GoingNegative;
use Specifications\ErrorCases\Success;

class ServiceImplTest extends TestCase
{
    protected ServiceImpl $service;
    protected string $validUsername = 'giacomo.guidotto';
    protected string $validPassword = 'Fr6/ese342f';
    protected string $validDeposit = 'test deposit';
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

        echo json_encode($testedToken, JSON_PRETTY_PRINT);

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

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

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

        echo json_encode($testedArray, JSON_PRETTY_PRINT);
    }

    // ==== Get the user information ===========================================

    public function testUserInformation()
    {
        $testedArray = $this->service->getUser(
            $this->validToken
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

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
            'gSqwFKJnKRiY',
            $this->validPassword
        );

        echo json_encode($dummyToken, JSON_PRETTY_PRINT);

        $testedArray = $this->service->closeUser(
            $dummyToken['token']
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

        self::assertNull($testedArray);
    }

    // ==== Close a specific session ===========================================

    public function testCloseSession()
    {
        $testedArray = $this->service->closeSession(
            $this->validToken
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

        self::assertNull($testedArray);
    }

    // ==== Open a new deposit =================================================

    public function testDepositCreation()
    {
        $generatedName = $this->generateString();

        $testedArray = $this->service->createDeposit(
            $this->validToken,
            $generatedName,
            'standard',
            null
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

        self::assertEquals(
            $generatedName,
            $testedArray['name']
        );
        self::assertEquals(
            'standard',
            $testedArray['type']
        );
        self::assertEquals(
            0,
            $testedArray['amount']
        );
    }

    // ==== Get the deposit information ========================================

    public function testGetDepositList()
    {
        $testedArray = $this->service->getDeposits(
            $this->validToken,
            null
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

        self::assertNotEmpty($testedArray);
    }

    public function testGetDeposit()
    {
        $testedArray = $this->service->getDeposits(
            $this->validToken,
            'lYIQRiRA'
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

        self::assertEquals(
            Success::CODE,
            DepositImpl::validateName($testedArray['name'])
        );
        self::assertEquals(
            Success::CODE,
            DepositImpl::validateAmount($testedArray['amount'])
        );
        self::assertEquals(
            Success::CODE,
            DepositImpl::validateType($testedArray['type'])
        );
    }

    // ==== Freeze a specific deposit ==========================================

    public function testDeleteDeposit()
    {
        $testedArray = $this->service->closeDeposit(
            $this->validToken,
            'DKkzxKPD',
            $this->validDeposit,
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

        self::assertNotEmpty($testedArray);
    }

    // ==== Update the deposit amount ==========================================

    public function testUpdateDeposit()
    {
        $testedArray = $this->service->updateDeposit(
            $this->validToken,
            $this->validDeposit,
            'deposit',
            1000
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);


        self::assertEquals(
            Success::CODE,
            DepositImpl::validateName($testedArray['name'])
        );
        self::assertEquals(
            Success::CODE,
            DepositImpl::validateAmount($testedArray['amount'])
        );
        self::assertEquals(
            Success::CODE,
            DepositImpl::validateType($testedArray['type'])
        );
    }

    public function testWithdrawTooMuch()
    {
        $testedArray = $this->service->updateDeposit(
            $this->validToken,
            $this->validDeposit,
            'withdraw',
            10000
        );

        echo json_encode($testedArray, JSON_PRETTY_PRINT);

        self::assertEquals(
            GoingNegative::CODE,
            $testedArray['error']
        );
    }
}
