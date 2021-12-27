<?php

namespace Model\Loan;

use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMaxRange;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\ExceedingMinRange;
use Specifications\ErrorCases\IncorrectParsing;
use Specifications\ErrorCases\IncorrectPattern;
use Specifications\ErrorCases\Success;

class LoanImpl implements Loan
{

    /**
     * @inheritDoc
     */
    public static function validateName(string $name): int
    {
        if (strlen($name) > 64) {
            return ExceedingMaxLength::CODE;
        }
        if (strlen($name) < 1) {
            return ExceedingMinLength::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritDoc
     */
    public static function validateAmountBorrowed(int $amount): int
    {
        if ($amount > 2 ** 31 - 1) {
            return ExceedingMaxRange::CODE;
        }
        if ($amount < -(2 ** 31 - 1)) {
            return ExceedingMinRange::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritDoc
     */
    public static function validateInterestRate(float $rate): int
    {
        if ($rate > 9.9999) {
            return ExceedingMaxRange::CODE;
        }
        if ($rate < 0.0001) {
            return ExceedingMinRange::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritDoc
     */
    public static function validateMonthlyRate(int $amount): int
    {
        if ($amount > 2 ** 31 - 1) {
            return ExceedingMaxRange::CODE;
        }
        if ($amount < -(2 ** 31 - 1)) {
            return ExceedingMinRange::CODE;
        }

        return Success::CODE;
    }

    /**
     * @inheritDoc
     */
    public static function validateRepaymentDay(string $timestamp): int
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
     * @inheritDoc
     */
    public static function validateType(string $type): int
    {
        $enum = ['secured'];

        if (!in_array($type, $enum, true)) {
            return IncorrectParsing::CODE;
        }

        return Success::CODE;
    }

}