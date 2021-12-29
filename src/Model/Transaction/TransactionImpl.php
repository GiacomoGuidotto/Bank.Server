<?php

namespace Model\Transaction;

use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMaxRange;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\ExceedingMinRange;
use Specifications\ErrorCases\IncorrectParsing;
use Specifications\ErrorCases\IncorrectPattern;
use Specifications\ErrorCases\Success;
use Specifications\ErrorCases\UpdateWithZero;

class TransactionImpl implements Transaction
{
    /**
     * @inheritdoc
     */
    public static function validateType(string $type): int
    {
        $enum = ['withdraw', 'deposit'];

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
        if ($amount == 0) {
            return UpdateWithZero::CODE;
        }
        if ($amount > 2 ** 31 - 1) {
            return ExceedingMaxRange::CODE;
        }
        if ($amount < 0) {
            return ExceedingMinRange::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritdoc
     */
    public static function validateTimestamp(string $timestamp): int
    {
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
        if (strlen($author) > 129) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($author) < 1) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }
}