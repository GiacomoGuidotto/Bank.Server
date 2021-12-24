<?php

namespace Services\Database;

use Model\User\UserImpl;
use Specifications\Bank\Bank;
use Specifications\ErrorCases\AlreadyExist;
use Specifications\ErrorCases\ErrorCases;
use Specifications\ErrorCases\NotFound;
use Specifications\ErrorCases\Success;

class ServiceImpl implements Service
{
    private Module $module;

    public function __construct()
    {
        $this->module = new Module();
    }

    private function generateErrorMessage(int $code): array
    {
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'error' => $code,
            'message' => ErrorCases::ERROR_MESSAGES[$code],
            'details' => ErrorCases::ERROR_DETAILS[$code]
        ];
    }

    // ==== Get the session token ==============================================

    /**
     * @inheritDoc
     */
    public function authenticate(string $username, string $password): array
    {
        $usernameValidation = UserImpl::validateUsername($username);
        $passwordValidation = UserImpl::validatePassword($password);

        if ($usernameValidation != Success::CODE) {
            return $this->generateErrorMessage($usernameValidation);
        }
        if ($passwordValidation != Success::CODE) {
            return $this->generateErrorMessage($passwordValidation);
        }

        // =======================================
        $this->module->beginTransaction();

        // ==== correct username and password ====
        $storedPasswordRow = $this->module->executeQuery('
            SELECT password 
            FROM users 
            WHERE username = :username',
            [
                ':username' => $username
            ]
        );

        // ==== username not found ===============
        if (count($storedPasswordRow) != 1) {
            return $this->generateErrorMessage(NotFound::CODE);
        }

        // ==== password incorrect ===============
        $storedPassword = $storedPasswordRow[0]['password'];
        if (!password_verify($password, $storedPassword)) {
            return $this->generateErrorMessage(NotFound::CODE);
        }

        // ==== create token =====================
        $userId = $this->module->executeQuery('
            SELECT user_id FROM users WHERE username = :username
        ',
            [
                ':username' => $username
            ])[0]['user_id'];


        $this->module->executeQuery('
            INSERT INTO sessions (session_token, user, creation_timestamp, last_updated, active)
            VALUES (
                    UUID(),
                    :user_id,
                    CURRENT_TIMESTAMP(),
                    CURRENT_TIMESTAMP(),
                    :active
            ) 
        ',
            [
                ':user_id' => $userId,
                ':active' => true
            ]);

        $token = $this->module->executeQuery('
            SELECT session_token FROM sessions WHERE session_id = LAST_INSERT_ID()
        ')[0]['session_token'];

        $this->module->commitTransaction();

        return [
            'token' => $token
        ];
    }

    // ==== Create a new user ==================================================

    /**
     * @inheritDoc
     */
    public function createUser(
        string $username,
        string $password,
        string $name,
        string $surname
    ): array
    {
        $usernameValidation = UserImpl::validateUsername($username);
        $passwordValidation = UserImpl::validatePassword($password);
        $nameValidation = UserImpl::validateName($name);
        $surnameValidation = UserImpl::validateSurname($surname);

        if ($usernameValidation != Success::CODE) {
            return $this->generateErrorMessage($usernameValidation);
        }
        if ($passwordValidation != Success::CODE) {
            return $this->generateErrorMessage($passwordValidation);
        }
        if ($nameValidation != Success::CODE) {
            return $this->generateErrorMessage($nameValidation);
        }
        if ($surnameValidation != Success::CODE) {
            return $this->generateErrorMessage($surnameValidation);
        }

        // =======================================
        $this->module->beginTransaction();

        // ==== Already exist checks =============
        $user = $this->module->executeQuery(
            'SELECT username, name FROM users WHERE username = :username',
            [
                ':username' => $username
            ]
        );

        if (count($user) != 0) {
            return $this->generateErrorMessage(AlreadyExist::CODE);
        }

        // ==== IBAN resolution ==================
        $lastId = $this->module->executeQuery(
            'SELECT MAX(user_id) FROM users'
        )[0][0];
        $accountNumber = str_pad($lastId, 12, '0', STR_PAD_LEFT);
        $IBAN =
            Bank::COUNTRY_CODE .
            '99' .
            'X' .
            Bank::BANK_CODE .
            Bank::BRANCH_CODE .
            $accountNumber;

        // ==== Securing password ================
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // ==== Insert query =====================
        $this->module->executeQuery('
            INSERT INTO users (username, password, name, surname, IBAN, active)
            VALUES (
                    :username,
                    :password,
                    :name,
                    :surname,
                    :IBAN,
                    :active
            )',
            [
                ':username' => $username,
                ':password' => $hashedPassword,
                ':name' => $name,
                ':surname' => $surname,
                ':IBAN' => $IBAN,
                ':active' => true
            ]
        );

        $this->module->commitTransaction();

        return [
            'username' => $username,
            'name' => $name,
            'surname' => $surname,
            'IBAN' => $IBAN
        ];
    }

    // ==== Get the user information ===========================================

    /**
     * @inheritDoc
     */
    public function getUser(string $token): array
    {
        // TODO: Implement getUser() method.
    }

    // ==== Close a specific user ==============================================

    /**
     * @inheritDoc
     */
    public function closeUser(string $token): array|null
    {
        // TODO: Implement closeUser() method.
    }

    // ==== Close a specific session ===========================================

    /**
     * @inheritDoc
     */
    public function closeSession(string $token): array|null
    {
        // TODO: Implement closeSession() method.
    }

    // ==== Get the deposit information ========================================

    /**
     * @inheritDoc
     */
    public function getDeposits(string $token, string $name = null): array
    {
        // TODO: Implement getDeposits() method.
    }

    // ==== Open a new deposit =================================================

    /**
     * @inheritDoc
     */
    public function createDeposit(string $token, string $name, string $type, int $amount): array
    {
        // TODO: Implement createDeposit() method.
    }

    // ==== Freeze a specific deposit ==========================================

    /**
     * @inheritDoc
     */
    public function closeDeposit(string $token, string $name, string $destinationDeposit): array
    {
        // TODO: Implement closeDeposit() method.
    }

    // ==== Update the deposit amount ==========================================

    /**
     * @inheritDoc
     */
    public function updateDeposit(string $token, string $name, string $action, int $amount): array
    {
        // TODO: Implement updateDeposit() method.
    }

    // ==== Get the deposit's transaction history ==============================

    /**
     * @inheritDoc
     */
    public function getHistory(string $token, string $name): array
    {
        // TODO: Implement getHistory() method.
    }

    // ==== get the loans information ==========================================

    /**
     * @inheritDoc
     */
    public function getLoans(string $token, string $name = null): array
    {
        // TODO: Implement getLoans() method.
    }

    // ==== Open a new loan ====================================================

    /**
     * @inheritDoc
     */
    public function createLoan(string $token, string $deposit, string $name, string $amountAsked, string $repaymentDay, string $type): array
    {
        // TODO: Implement createLoan() method.
    }

    // ==== Conclude a specific loan ===========================================

    /**
     * @inheritDoc
     */
    public function closeLoan(string $token, string $name): array
    {
        // TODO: Implement closeLoan() method.
    }
}