<?php

namespace Model\Deposit;

use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMaxRange;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\ExceedingMinRange;
use Specifications\ErrorCases\IncorrectParsing;
use Specifications\ErrorCases\Success;

class DepositImpl implements Deposit
{
    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public static function validateAmount(int $amount): int
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
     * @inheritdoc
     */
    public static function validateType(string $type): int
    {
        $enum = ['standard'];

        if (!in_array($type, $enum, true)) {
            return IncorrectParsing::CODE;
        }

        return Success::CODE;
    }

}