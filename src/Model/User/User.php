<?php

namespace Model\User;

/**
 * User Interface, declare specific methods for the user resource
 *
 * @category Interface
 */
interface User
{
    /**
     * Check the constrains of the username attribute
     *
     * @param string $username the username to check
     * @return int             either the error code
     *                         or the success code
     */
    public static function validateUsername(string $username): int;

    /**
     * Check the constrains of the password attribute
     *
     * @param string $password the password to check
     * @return int             either the error code
     *                         or the success code
     */
    public static function validatePassword(string $password): int;

    /**
     * Check the constrains of the name attribute
     *
     * @param string $name the name to check
     * @return int         either the error code
     *                     or the success code
     */
    public static function validateName(string $name): int;

    /**
     * Check the constrains of the surname attribute
     *
     * @param string $surname the surname to check
     * @return int            either the error code
     *                        or the success code
     */
    public static function validateSurname(string $surname): int;

    /**
     * Check the constrains of the IBAN attribute
     *
     * @param string $IBAN the IBAN to check
     * @return int         either the error code
     *                     or the success code
     */
    public static function validateIBAN(string $IBAN): int;
}