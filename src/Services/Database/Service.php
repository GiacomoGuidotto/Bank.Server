<?php

namespace Services\Database;

interface Service
{
    // ==== Get the session token ==============================================

    /**
     * Get the session token
     * Retrieve the session token with the username/password combination
     *
     * @param string $username the username to authenticate
     *                         extracted from the request
     * @param string $password the user password to authenticate
     *                         extracted from the request
     * @return array           the token used to authenticate the user
     *                         saved in an array as object
     */
    public function authenticate(string $username, string $password): array;

    // ==== Create a new user ==================================================

    /**
     * Create a new user
     * create a new user using the given parameters
     *
     * @param string $username the user identifier
     *                         extracted from the request
     * @param string $password the clear password of the account
     *                         extracted from the request
     * @param string $name the user's name
     *                         extracted from the request
     * @param string $surname the user's surname
     *                         extracted from the request
     * @return array           the public attributes of the user
     *                         with the calculated IBAN
     *                         saved in an array as object
     */
    public function createUser(
        string $username,
        string $password,
        string $name,
        string $surname
    ): array;

    // ==== Get the user information ===========================================

    /**
     * Get the user information
     * Retrieve user's name, surname, IBAN from the database
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @return array        the public attributes of the user
     *                      saved in an array as object
     */
    public function getUser(string $token): array;

    // ==== Close a specific user ==============================================

    /**
     * Close a specific user
     * Make a specific user unreachable
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @return array|null   the eventual error array as object
     */
    public function closeUser(string $token): array|null;

    // ==== Close a specific session ===========================================

    /**
     * Close a specific session
     * Make a specific session token unreachable
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @return array|null   the eventual error array as object
     */
    public function closeSession(string $token): array|null;

    // ==== Get the deposit information ========================================

    /**
     * Get the deposits information
     * Retrieve the list of deposit's name from the database.
     * If a deposit name is given, the query is filtered and
     * is retrieved the specific deposit's name, amount,
     * type from the database.
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string|null $name the optional filter
     *                          extracted from the request
     * @return array the list of deposits or the single
     *               deposit saved in an array as object
     */
    public function getDeposits(string $token, string|null $name): array;

    // ==== Open a new deposit =================================================

    /**
     * Open a new deposit
     * create a new deposit using the given parameters
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string $name the deposit identifier (its name)
     *                     extracted from the request
     * @param string $type the deposit type
     *                     extracted from the request
     * @param int|null $amount the conditional deposit initial amount
     *                         extracted from the request
     * @return array the public attributes of the deposit
     *               saved in an array as a object
     */
    public function createDeposit(
        string   $token,
        string   $name,
        string   $type,
        int|null $amount
    ): array;

    // ==== Freeze a specific deposit ==========================================

    /**
     * Freeze a specific deposit
     * Make a specific deposit unreachable
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string $name the deposit identifier (its name)
     *                     extracted from the request
     * @param string $destinationDeposit the second deposit identifier
     *                                   extracted from the request
     * @return array the new deposits list
     *               saved in an array as a object
     */
    public function closeDeposit(
        string $token,
        string $name,
        string $destinationDeposit
    ): array;

    // ==== Freeze a specific deposit ==========================================

    /**
     * Delete a specific deposit
     * Make a specific deposit unreachable without caring about the
     * money transfer
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string $name the deposit identifier (its name)
     *                     extracted from the request
     * @return array the new deposits list
     *               saved in an array as a object
     */
    public function deleteDeposit(
        string $token,
        string $name
    ): array;

    // ==== Update the deposit amount ==========================================

    /**
     * Update the deposit amount
     * represent either a "Deposit" or a "Withdraw" of
     * a quantity of money in the specific deposit
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string $name the deposit identifier (its name)
     *                     extracted from the request
     * @param string $action the action performed
     *                       extracted from the request
     * @param int $amount the amount of the change
     *                    extracted from the request
     * @return array the public attributes of the deposit
     *               saved in an array as a object
     */
    public function updateDeposit(
        string $token,
        string $name,
        string $action,
        int    $amount
    ): array;

    // ==== Get the deposit's transaction history ==============================

    /**
     * Get the deposit's transaction history
     * Retrieve the deposit's transaction history list:
     * transaction type, amount deducted, author and timestamp
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string $name the deposit identifier (its name)
     *                     extracted from the request
     * @return array the list of public attributes of the transaction
     *               saved in an array as a object
     */
    public function getHistory(string $token, string $name): array;

    // ==== get the loans information ==========================================

    /**
     * Get the loans information
     * Retrieve the list of loan's name from the database.
     * If either a loan name or the linked deposit is given,
     * the query is filtered and is retrieved the specific
     * loan's name, total amount of money borrowed, amount
     * of money deducted monthly, the interest rate, the
     * repayment day, type from the database.
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string|null $name the optional filter
     *                          extracted from the request
     * @return array the list of loans or the single
     *               loan saved in an array as object
     */
    public function getLoans(string $token, string $name = null): array;

    // ==== Open a new loan ====================================================

    /**
     * Open a new loan
     * create a new loan using the given parameters
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string $deposit the deposit linked from this loan
     *                        extracted from the request
     * @param string $name the loan identifier (its name)
     *                     extracted from the request
     * @param string $amountAsked the loan's amount asked
     *                            extracted from the request
     * @param string $repaymentDay the loan's repayment day
     *                             extracted from the request
     * @param string $type the loan's type
     *                     extracted from the request
     * @return array the public attributes of the loan
     *               saved in an array as a object
     */
    public function createLoan(
        string $token,
        string $deposit,
        string $name,
        string $amountAsked,
        string $repaymentDay,
        string $type
    ): array;

    // ==== Conclude a specific loan ===========================================

    /**
     * Conclude a specific loan
     * Deduct the remaining amount of money required
     * from the loan
     *
     * @param string $token the token used to authenticate the user
     *                      extracted from the request
     * @param string $name the loan identifier (its name)
     *                     extracted from the request
     * @return array the new loans list
     *               saved in an array as a object
     */
    public function closeLoan(string $token, string $name): array;
}