<?php

namespace Model\Loan;

/**
 * Loan Interface, declare specific methods for the loan resource
 *
 * @category Interface
 */
interface Loan
{
    /**
     * Checks the constrains of the loan's name attribute
     *
     * @param string $name the name to check
     * @return int         either the error code
     *                     or the success code
     */
    public static function validateName(string $name): int;

    /**
     * Checks the constrains of the loan's amount borrowed attribute
     *
     * @param int $amount the amount to check
     * @return int        either the error code
     *                    or the success code
     */
    public static function validateAmountBorrowed(int $amount): int;

    /**
     * Checks the constrains of the loan's interest rate attribute
     *
     * @param float $rate the interest rate to check
     * @return int        either the error code
     *                    or the success code
     */
    public static function validateInterestRate(float $rate): int;

    /**
     * Checks the constrains of the loan's monthly rate attribute
     *
     * @param int $amount the amount of the monthly rate to check
     * @return int        either the error code
     *                    or the success code
     */
    public static function validateMonthlyRate(int $amount): int;

    /**
     * Checks the constrains of the loan's repayment day attribute
     *
     * @param string $timestamp the day to check
     * @return int              either the error code
     *                          or the success code
     */
    public static function validateRepaymentDay(string $timestamp): int;

    /**
     * Checks the constrains of the loan's type attribute
     *
     * @param string $type the type to check
     * @return int         either the error code
     *                     or the success code
     */
    public static function validateType(string $type): int;
}