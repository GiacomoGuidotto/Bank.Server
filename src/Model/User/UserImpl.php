<?php

namespace Model\User;

use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\IncorrectPattern;
use Specifications\ErrorCases\NullAttributes;
use Specifications\ErrorCases\Success;

/**
 * UserImpl Implementation, the representation of the user resource
 * through the general Object Model Interface
 * and the UserImpl Interface
 *
 * @category Class
 */
class UserImpl implements User
{
    /**
     * @inheritdoc
     */
    public static function validateUsername(string $username): int
    {
        if ($username == null) {
            return NullAttributes::CODE;
        }
        if (strlen($username) > 64) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($username) < 1) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritdoc
     */
    public static function validatePassword(string $password): int
    {
        if ($password == null) {
            return NullAttributes::CODE;
        }
        if (strlen($password) > 32) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($password) < 8) {
            return ExceedingMinLength::CODE;
        }
        if (preg_match("#^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,32}$#", $password) != 1) {
            return IncorrectPattern::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritdoc
     */
    public static function validateName(string $name): int
    {
        if ($name == null) {
            return NullAttributes::CODE;
        }
        if (strlen($name) > 64) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($name) < 1) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritdoc
     */
    public static function validateSurname(string $surname): int
    {
        if ($surname == null) {
            return NullAttributes::CODE;
        }
        if (strlen($surname) > 64) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($surname) < 1) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritdoc
     */
    public static function validateIBAN(string $IBAN): int
    {
        // IBAN checks
        if ($IBAN == null) {
            return NullAttributes::CODE;
        }
        if (strlen($IBAN) > 32) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($IBAN) < 15) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }
}