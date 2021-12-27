<?php

namespace Services\Database;

use DateInterval;
use JetBrains\PhpStorm\ArrayShape;
use Model\Session\SessionImpl;
use Model\User\UserImpl;
use Specifications\Bank\Bank;
use Specifications\Database\Database;
use Specifications\ErrorCases\AlreadyExist;
use Specifications\ErrorCases\ErrorCases;
use Specifications\ErrorCases\NotFound;
use Specifications\ErrorCases\Success;
use Specifications\ErrorCases\Timeout;
use Specifications\ErrorCases\Unauthorized;

class ServiceImpl implements Service
{
    private Module $module;

    public function __construct()
    {
        $this->module = new Module();
    }

    #[ArrayShape([
        'timestamp' => "string",
        'error' => "int",
        'message' => "string",
        'details' => "string"
    ])] public function generateErrorMessage(int $code): array
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
        $storedPasswordRow = $this->module->fetchOne(
            'SELECT password 
                   FROM users 
                   WHERE username = :username AND active = TRUE',
            [
                ':username' => $username
            ]
        );

        // ==== username not found ===============
        if (!$storedPasswordRow) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(NotFound::CODE);
        }

        // ==== password incorrect ===============
        $storedPassword = $storedPasswordRow['password'];
        if (!password_verify($password, $storedPassword)) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(NotFound::CODE);
        }

        $userId = $this->module->fetchOne(
            'SELECT user_id FROM users WHERE username = :username',
            [
                ':username' => $username
            ]
        )['user_id'];

        // ==== delete token already existing ====
        $this->module->execute(
            'UPDATE sessions
                   SET active = FALSE
                   WHERE user = :user_id',
            [
                ':user_id' => $userId
            ]
        );

        // ==== create token =====================
        $this->module->execute(
            'INSERT 
                   INTO sessions (session_token, user, creation_timestamp, last_updated, active)
                   VALUES (
                        UUID(),
                        :user_id,
                        CURRENT_TIMESTAMP(),
                        CURRENT_TIMESTAMP(),
                        :active
                   )',
            [
                ':user_id' => $userId,
                ':active' => true
            ]
        );

        $token = $this->module->fetchOne(
            'SELECT session_token FROM sessions WHERE session_id = LAST_INSERT_ID()'
        )['session_token'];

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
        $user = $this->module->fetchOne(
            'SELECT username, name FROM users WHERE username = :username',
            [
                ':username' => $username
            ]
        );

        if ($user) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(AlreadyExist::CODE);
        }

        // ==== IBAN resolution ==================
        $lastId = $this->module->fetchOne(
            'SELECT MAX(user_id) FROM users'
        )['MAX(user_id)'];
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
        $this->module->execute(
            'INSERT 
                   INTO users (username, password, name, surname, IBAN, active)
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

    // ==== MZ - Militarized Zone ==============================================
    // =========================================================================

    /**
     * Utility Method
     * Calculate the time different between two date in full-time format
     *
     * @param string $finalTime the final time
     * @param string $initialTime the initial time
     * @return DateInterval the time difference in full-time format
     */
    private function timeDifference(string $finalTime, string $initialTime): DateInterval
    {
        $finalDate = date_create($finalTime);
        $initialDate = date_create($initialTime);

        return $finalDate->diff($initialDate, true);
    }

    /**
     * Utility method
     * Checks if the call exceeded the TTL duration
     *
     * @param string $currentTimestamp now
     * @param string $last_updated the moment of the last call
     * @return bool the result, true if still in time
     */
    private function validateTimeout(string $currentTimestamp, string $last_updated): bool
    {
        $timeToLive = date_create('midnight')
            ->add(DateInterval::createFromDateString(
                Database::SESSION_DURATION
            ));
        $timeDifference = date_create('midnight')
            ->add($this->timeDifference(
                $currentTimestamp,
                $last_updated
            ));

        return $timeDifference < $timeToLive;
    }

    /**
     * Validate the existence of the token
     * AND update the last_updated attribute
     * IF it does not exceeded the token duration
     *
     * @param string $token the token to verify
     * @return int          either the error code
     *                      or the success code
     */
    private function authorizeToken(string $token): int
    {
        // =======================================
        $this->module->beginTransaction();

        // ==== Find token =======================
        $token_row = $this->module->fetchOne(
            'SELECT last_updated 
                   FROM sessions 
                   WHERE session_token = :session_token AND active = TRUE',
            [
                ':session_token' => $token
            ]
        );

        if (!$token_row) {
            $this->module->commitTransaction();
            return Unauthorized::CODE;
        }

        // ==== Update session TTL ===============
        $last_updated = $token_row['last_updated'];

        $current_timestamp = $this->module->fetchOne(
            'SELECT CURRENT_TIMESTAMP()'
        )['CURRENT_TIMESTAMP()'];

        if (!$this->validateTimeout($current_timestamp, $last_updated)) {
            // ==== If timeout =======================
            $this->module->execute(
                'UPDATE sessions
                       SET active = FALSE
                       WHERE session_token = :session_token',
                [
                    ':session_token' => $token
                ]
            );

            $this->module->commitTransaction();
            return Timeout::CODE;
        }

        $this->module->execute(
            'UPDATE sessions
                   SET last_updated = CURRENT_TIMESTAMP()
                   WHERE session_token = :session_token',
            [
                ':session_token' => $token
            ]
        );

        $this->module->commitTransaction();
        return Success::CODE;
    }

    // ==== Get the user information ===========================================

    /**
     * @inheritDoc
     */
    public function getUser(string $token): array
    {
        $tokenValidation = SessionImpl::validateToken($token);

        if ($tokenValidation != Success::CODE) {
            return $this->generateErrorMessage($tokenValidation);
        }

        // ==== Token authorization ==============
        $tokenAuthorization = $this->authorizeToken($token);

        if ($tokenAuthorization != Success::CODE) {
            return $this->generateErrorMessage($tokenAuthorization);
        }

        // =======================================
        $this->module->beginTransaction();

        $user_id = $this->module->fetchOne(
            'SELECT user FROM sessions WHERE session_token = :session_token',
            [
                ':session_token' => $token
            ]
        )['user'];

        $user = $this->module->fetchOne(
            'SELECT username, name, surname, IBAN FROM users WHERE user_id = :user_id',
            [
                ':user_id' => $user_id
            ]
        );

        $this->module->commitTransaction();
        return [
            'username' => $user['username'],
            'name' => $user['name'],
            'surname' => $user['surname'],
            'IBAN' => $user['IBAN']
        ];
    }

    // ==== Close a specific user ==============================================

    /**
     * @inheritDoc
     */
    public function closeUser(string $token): array|null
    {
        $tokenValidation = SessionImpl::validateToken($token);

        if ($tokenValidation != Success::CODE) {
            return $this->generateErrorMessage($tokenValidation);
        }

        // ==== Token authorization ==============
        $tokenAuthorization = $this->authorizeToken($token);

        if ($tokenAuthorization != Success::CODE) {
            return $this->generateErrorMessage($tokenAuthorization);
        }

        // =======================================
        $this->module->beginTransaction();

        $user_id = $this->module->fetchOne(
            'SELECT user FROM sessions WHERE session_token = :session_token',
            [
                ':session_token' => $token
            ]
        )['user'];

        var_dump($user_id);

        $this->module->execute(
            'UPDATE users
                   SET active = 0
                   WHERE user_id = :user_id',
            [
                ':user_id' => $user_id
            ]
        );

        $this->module->commitTransaction();

        // TODO call closeDeposits and closeLoans() methods on cascade
        $this->closeSession($token);
        return null;
    }

    // ==== Close a specific session ===========================================

    /**
     * @inheritDoc
     */
    public function closeSession(string $token): array|null
    {
        $tokenValidation = SessionImpl::validateToken($token);

        if ($tokenValidation != Success::CODE) {
            return $this->generateErrorMessage($tokenValidation);
        }

        // =======================================
        $this->module->beginTransaction();

        // ==== Find token =======================
        $token_row = $this->module->fetchOne(
            'SELECT last_updated 
                   FROM sessions 
                   WHERE session_token = :session_token AND active = TRUE',
            [
                ':session_token' => $token
            ]
        );

        if (!$token_row) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(Unauthorized::CODE);
        }

        // ==== Disable token ====================
        $this->module->execute(
            'UPDATE sessions
                   SET active = FALSE
                   WHERE session_token = :session_token',
            [
                ':session_token' => $token
            ]
        );

        $this->module->commitTransaction();
        return null;
    }

    // ==== Get the deposit information ========================================

    /**
     * @inheritDoc
     */
    public function getDeposits(string $token, string $name = null): array
    {
        // TODO: Implement getDeposits() method.
        return [];
    }

    // ==== Open a new deposit =================================================

    /**
     * @inheritDoc
     */
    public function createDeposit(string $token, string $name, string $type, int $amount): array
    {
        // TODO: Implement createDeposit() method.
        return [];
    }

    // ==== Freeze a specific deposit ==========================================

    /**
     * @inheritDoc
     */
    public function closeDeposit(string $token, string $name, string $destinationDeposit): array
    {
        // TODO: Implement closeDeposit() method.
        return [];
    }

    // ==== Update the deposit amount ==========================================

    /**
     * @inheritDoc
     */
    public function updateDeposit(string $token, string $name, string $action, int $amount): array
    {
        // TODO: Implement updateDeposit() method.
        return [];
    }

    // ==== Get the deposit's transaction history ==============================

    /**
     * @inheritDoc
     */
    public function getHistory(string $token, string $name): array
    {
        // TODO: Implement getHistory() method.
        return [];
    }

    // ==== get the loans information ==========================================

    /**
     * @inheritDoc
     */
    public function getLoans(string $token, string $name = null): array
    {
        // TODO: Implement getLoans() method.
        return [];
    }

    // ==== Open a new loan ====================================================

    /**
     * @inheritDoc
     */
    public function createLoan(string $token, string $deposit, string $name, string $amountAsked, string $repaymentDay, string $type): array
    {
        // TODO: Implement createLoan() method.
        return [];
    }

    // ==== Conclude a specific loan ===========================================

    /**
     * @inheritDoc
     */
    public function closeLoan(string $token, string $name): array
    {
        // TODO: Implement closeLoan() method.
        return [];
    }
}