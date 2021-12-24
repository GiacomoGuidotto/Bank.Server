<?php

namespace Model\Session;

use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\IncorrectPattern;
use Specifications\ErrorCases\NullAttributes;
use Specifications\ErrorCases\Success;

class SessionImpl implements Session
{

    /**
     * @inheritDoc
     */
    public static function validateToken(string $token): int
    {
        if ($token == null) {
            return NullAttributes::CODE;
        }
        if (strlen($token) > 36) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($token) < 36) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritDoc
     */
    public static function validateCreationTimestamp(string $timestamp): int
    {
        if ($timestamp == null) {
            return NullAttributes::CODE;
        }
        if (strlen($timestamp) > 19) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($timestamp) < 19) {
            return ExceedingMinLength::CODE;
        }
        // e.g. 2021-12-25 12:00:00
        if (preg_match(
                "#([0-9]{4})-(0[1-9]|1[1|2])-([0-2][0-9]|3[0|1]) ([0|1][0-9]|2[0-3])(:[0-5][0-9]){2}#",
                $timestamp
            ) != 1) {
            return IncorrectPattern::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritDoc
     */
    public static function validateLastUpdated(string $timestamp): int
    {
        if ($timestamp == null) {
            return NullAttributes::CODE;
        }
        if (strlen($timestamp) > 19) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($timestamp) < 19) {
            return ExceedingMinLength::CODE;
        }
        // e.g. 2021-12-25 12:00:00
        if (preg_match(
                "#([0-9]{4})-(0[1-9]|1[1|2])-([0-2][0-9]|3[0|1]) ([0|1][0-9]|2[0-3])(:[0-5][0-9]){2}#",
                $timestamp
            ) != 1) {
            return IncorrectPattern::CODE;
        }

        return Success::CODE;
    }
}