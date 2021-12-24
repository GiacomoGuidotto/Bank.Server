<?php

namespace Model\Deposit;

use Specifics\ErrorCases\ExceedingMaxLength;
use Specifics\ErrorCases\ExceedingMaxRange;
use Specifics\ErrorCases\ExceedingMinLength;
use Specifics\ErrorCases\ExceedingMinRange;
use Specifics\ErrorCases\IncorrectParsing;
use Specifics\ErrorCases\NullAttributes;
use Specifics\ErrorCases\Success;

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