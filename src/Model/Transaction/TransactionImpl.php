<?php

namespace Model\Transaction;

use Specifics\ErrorCases\ExceedingMaxLength;
use Specifics\ErrorCases\ExceedingMaxRange;
use Specifics\ErrorCases\ExceedingMinLength;
use Specifics\ErrorCases\IncorrectParsing;
use Specifics\ErrorCases\IncorrectPattern;
use Specifics\ErrorCases\NullAttributes;
use Specifics\ErrorCases\Success;

class TransactionImpl implements Transaction
{
    /**
     * @inheritdoc
     */
    public static function validateType(string $type): int
    {
        $enum = ['withdraw', 'deposit'];

        if ($type == null) {
            return NullAttributes::CODE;
        }
        if (!in_array($type, $enum, true)) {
            return IncorrectParsing::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritdoc
     */
    public static function validateAmount(int $amount): int
    {
        if ($amount == null) {
            return NullAttributes::CODE;
        }
        if ($amount > 2 ** 31 - 1) {
            return ExceedingMaxRange::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritdoc
     */
    public static function validateTimestamp(string $timestamp): int
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
     * @inheritdoc
     */
    public static function validateAuthor(string $author): int
    {
        if ($author == null) {
            return NullAttributes::CODE;
        }
        if (strlen($author) > 129) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($author) < 1) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }
}