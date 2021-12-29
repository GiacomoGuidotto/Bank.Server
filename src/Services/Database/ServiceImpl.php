<?php

namespace Services\Database;

use DateInterval;
use JetBrains\PhpStorm\ArrayShape;
use Model\Deposit\DepositImpl;
use Model\Session\SessionImpl;
use Model\Transaction\TransactionImpl;
use Model\User\UserImpl;
use Specifications\Bank\Bank;
use Specifications\Database\Database;
use Specifications\ErrorCases\AlreadyExist;
use Specifications\ErrorCases\ErrorCases;
use Specifications\ErrorCases\Forbidden;
use Specifications\ErrorCases\GoingNegative;
use Specifications\ErrorCases\InvalidDepositAmount;
use Specifications\ErrorCases\InvalidDestinationDeposit;
use Specifications\ErrorCases\NotFound;
use Specifications\ErrorCases\NullAttributes;
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
            'SELECT username, name 
                   FROM users 
                   WHERE username = :username AND active = TRUE',
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

        $this->module->execute(
            'UPDATE users
                   SET active = FALSE
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
    public function getDeposits(string $token, string|null $name): array
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

        // ==== fetch the entire list ============
        if ($name == null) {
            $deposits = $this->module->fetchAll(
                'SELECT name, amount, type 
                       FROM deposits
                       WHERE user = :user AND active = TRUE',
                [
                    ':user' => $user_id
                ]
            );

            $this->module->commitTransaction();

            // ==== deposit lists not found ======
            if (!$deposits) {
                return $this->generateErrorMessage(Forbidden::CODE);
            }

            $refactoredDeposits = [];

            foreach ($deposits as $deposit) {
                array_push($refactoredDeposits, [
                    'name' => $deposit['name'],
                    'amount' => $deposit['amount'],
                    'type' => $deposit['type']
                ]);
            }

            return $refactoredDeposits;
        } else {
            $deposit = $this->module->fetchOne(
                'SELECT name, amount, type 
                       FROM deposits
                       WHERE user = :user AND name = :name AND active = TRUE',
                [
                    ':user' => $user_id,
                    ':name' => $name
                ]
            );

            $this->module->commitTransaction();

            // ==== deposit lists not found ======
            if (!$deposit) {
                return $this->generateErrorMessage(Forbidden::CODE);
            }

            return [
                'name' => $deposit['name'],
                'amount' => $deposit['amount'],
                'type' => $deposit['type']
            ];
        }
    }

    // ==== Open a new deposit =================================================

    /**
     * @inheritDoc
     */
    public function createDeposit(string $token, string $name, string $type, int|null $amount): array
    {
        $tokenValidation = SessionImpl::validateToken($token);
        $nameValidation = DepositImpl::validateName($name);
        $typeValidation = DepositImpl::validateType($type);

        if ($tokenValidation != Success::CODE) {
            return $this->generateErrorMessage($tokenValidation);
        }
        if ($nameValidation != Success::CODE) {
            return $this->generateErrorMessage($nameValidation);
        }
        if ($typeValidation != Success::CODE) {
            return $this->generateErrorMessage($typeValidation);
        }

        // ==== Amount for saving deposit check ====

        if ($type == 'saving' && $amount == null) {
            return $this->generateErrorMessage(NullAttributes::CODE);
        }

        if ($type == 'saving' && $amount < Bank::MINIMUM_SAVING_AMOUNT) {
            return $this->generateErrorMessage(InvalidDepositAmount::CODE);
        }

        $refactoredAmount = $amount ?? 0;
        $amountValidation = DepositImpl::validateAmount($refactoredAmount);

        if ($amountValidation != Success::CODE) {
            return $this->generateErrorMessage($amountValidation);
        }

        // ==== Token authorization ==============
        $tokenAuthorization = $this->authorizeToken($token);

        if ($tokenAuthorization != Success::CODE) {
            return $this->generateErrorMessage($tokenAuthorization);
        }

        // =========================================
        $this->module->beginTransaction();

        // ==== Already exist checks ===============
        $deposit = $this->module->fetchOne(
            'SELECT name 
                   FROM deposits 
                   WHERE name = :name AND active = TRUE',
            [
                ':name' => $name
            ]
        );

        if ($deposit) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(AlreadyExist::CODE);
        }

        // ==== Elaboration ========================
        $user_id = $this->module->fetchOne(
            'SELECT user FROM sessions WHERE session_token = :session_token',
            [
                ':session_token' => $token
            ]
        )['user'];

        $this->module->execute(
            'INSERT 
                   INTO deposits (name, amount, type, user, active) 
                   VALUES (
                           :name,
                           :amount,
                           :type,
                           :user,
                           :active
                   )',
            [
                ':name' => $name,
                ':amount' => $refactoredAmount,
                ':type' => $type,
                ':user' => $user_id,
                ':active' => true
            ]
        );

        $this->module->commitTransaction();
        return [
            'name' => $name,
            'amount' => $refactoredAmount,
            'type' => $type
        ];
    }

    // ==== Freeze a specific deposit ==========================================

    /**
     * @inheritDoc
     */
    public function closeDeposit(string $token, string $name, string $destinationDeposit): array
    {
        $tokenValidation = SessionImpl::validateToken($token);
        $nameValidation = DepositImpl::validateName($name);
        $destinationDepositValidation = DepositImpl::validateName($destinationDeposit);

        if ($tokenValidation != Success::CODE) {
            return $this->generateErrorMessage($tokenValidation);
        }
        if ($nameValidation != Success::CODE) {
            return $this->generateErrorMessage($nameValidation);
        }
        if ($destinationDepositValidation != Success::CODE) {
            return $this->generateErrorMessage($destinationDepositValidation);
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

        // ==== Find destination deposit =========
        $destinationDepositRow = $this->module->fetchOne(
            'SELECT amount 
                   FROM deposits
                   WHERE user = :user AND name = :destination AND active = TRUE',
            [
                ':user' => $user_id,
                'destination' => $destinationDeposit
            ]
        );

        // ==== Destination deposit not found ====
        if (!$destinationDepositRow) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(InvalidDestinationDeposit::CODE);
        }

        $destinationDepositAmount = $destinationDepositRow['amount'];

        // ==== Find deposit to delete ===========
        $depositRow = $this->module->fetchOne(
            'SELECT amount
                   FROM deposits
                   WHERE user = :user AND name = :name AND active = TRUE',
            [
                ':user' => $user_id,
                ':name' => $name
            ]
        );

        // ==== Deposit to delete not found ======
        if (!$depositRow) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(Forbidden::CODE);
        }

        $depositAmount = $depositRow['amount'];

        // ==== Elaboration ======================
        $this->module->execute(
            'UPDATE deposits
                   SET amount = :amount
                   WHERE name = :destination',
            [
                ':amount' => $destinationDepositAmount + $depositAmount,
                ':destination' => $destinationDeposit
            ]
        );

        $this->module->execute(
            'UPDATE deposits
                   SET active = FALSE
                   WHERE name = :name',
            [
                ':name' => $name
            ]
        );

        $this->module->commitTransaction();
        return $this->getDeposits($token, null);
    }

    // ==== Update the deposit amount ==========================================

    /**
     * @inheritDoc
     */
    public function updateDeposit(string $token, string $name, string $action, int $amount): array
    {
        $tokenValidation = SessionImpl::validateToken($token);
        $nameValidation = DepositImpl::validateName($name);
        $actionValidation = TransactionImpl::validateType($action);
        $amountValidation = TransactionImpl::validateAmount($amount);

        if ($tokenValidation != Success::CODE) {
            return $this->generateErrorMessage($tokenValidation);
        }
        if ($nameValidation != Success::CODE) {
            return $this->generateErrorMessage($nameValidation);
        }
        if ($actionValidation != Success::CODE) {
            return $this->generateErrorMessage($actionValidation);
        }
        if ($amountValidation != Success::CODE) {
            return $this->generateErrorMessage($amountValidation);
        }

        // ==== Token authorization ==============
        $tokenAuthorization = $this->authorizeToken($token);

        if ($tokenAuthorization != Success::CODE) {
            return $this->generateErrorMessage($tokenAuthorization);
        }

        // =======================================
        $this->module->beginTransaction();

        $userId = $this->module->fetchOne(
            'SELECT user 
                   FROM sessions 
                   WHERE session_token = :session_token',
            [
                ':session_token' => $token
            ]
        )['user'];

        $depositRow = $this->module->fetchOne(
            'SELECT deposit_id, amount, type 
                   FROM deposits 
                   WHERE name = :name AND user = :user AND active = TRUE',
            [
                ':name' => $name,
                ':user' => $userId
            ]
        );

        // ==== Deposit not found ================
        if (!$depositRow) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(NotFound::CODE);
        }

        $depositAmount = $depositRow['amount'];

        $newAmount = $action == 'deposit' ? $depositAmount + $amount : $depositAmount - $amount;

        // ==== Withdraw going negative ==========
        if ($newAmount < 0) {
            $this->module->commitTransaction();
            return $this->generateErrorMessage(GoingNegative::CODE);
        }

        // ==== Valid transaction ================
        $this->module->execute(
            'UPDATE deposits
                   SET amount = :amount
                   WHERE name = :name',
            [
                ':amount' => $newAmount,
                ':name' => $name
            ]
        );

        // ==== save transaction =================
        $depositId = $depositRow['deposit_id'];

        $userRow = $this->module->fetchOne(
            'SELECT name, surname FROM users WHERE user_id = :user_id',
            [
                ':user_id' => $userId
            ]
        );
        $userName = $userRow['name'] . ' ' . $userRow['surname'];

        $this->module->execute(
            'INSERT 
                   INTO transactions (type, amount, timestamp, author, user, deposit)
                   VALUES (
                           :type,
                           :amount,
                           CURRENT_TIMESTAMP(),
                           :author,
                           :user,
                           :deposit
                   )',
            [
                ':type' => $action,
                ':amount' => $amount,
                ':author' => $userName,
                ':user' => $userId,
                ':deposit' => $depositId
            ]
        );

        $this->module->commitTransaction();
        return [
            'name' => $name,
            'amount' => $newAmount,
            'type' => $depositRow['type']
        ];
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