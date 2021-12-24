<?php

namespace Model\Deposit;

use Exception;

/**
 * Deposit Interface, declare specific methods for the deposit resource
 *
 * @category Interface
 */
interface Deposit
{
    /**
     * Checks the constrains of the name attribute
     *
     * @param string $name the name to check
     * @return int         either the error code
     *                     or the success code
     */
    public static function validateName(string $name): int;

    /**
     * Checks the constrains of the amount attribute
     *
     * @param int $amount the amount to check
     * @return int        either the error code
     *                    or the success code
     */
    public static function validateAmount(int $amount): int;

    /**
     * Checks the constrains of the type attribute
     *
     * @param string $type the type to check
     * @return int         either the error code
     *                     or the success code
     */
    public static function validateType(string $type): int;
}