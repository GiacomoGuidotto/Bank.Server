<?php

namespace Model\Deposit;

use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMaxRange;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\ExceedingMinRange;
use Specifications\ErrorCases\IncorrectParsing;
use Specifications\ErrorCases\NullAttributes;
use Specifications\ErrorCases\Success;

class DepositImpl implements Deposit
{
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
    public static function validateAmount(int $amount): int
    {
        if ($amount == null) {
            return NullAttributes::CODE;
        }
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

        if ($type == null) {
            return NullAttributes::CODE;
        }
        if (!in_array($type, $enum, true)) {
            return IncorrectParsing::CODE;
        }

        return Success::CODE;
    }

}