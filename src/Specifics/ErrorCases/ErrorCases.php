<?php
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Specifics\ErrorCases;

/**
 * Error cases schemas:
 * code, message, details
 *
 * ==== success case =========
 * 00 Success
 *
 * ==== generic ==============
 * 10 Null attributes
 *
 * ==== string-related =======
 * 20 Exceeding max length
 * 21 Exceeding min length
 * 22 Incorrect parsing
 * 23 Incorrect pattern
 *
 * ==== int-related ==========
 * 30 Exceeding max range
 * 31 Exceeding min range
 */

define('ErrorMessages', [
    Success::CODE => Success::MESSAGE,
    NullAttributes::CODE => NullAttributes::MESSAGE,
    ExceedingMaxLength::CODE => ExceedingMaxLength::MESSAGE,
    ExceedingMinLength::CODE => ExceedingMinLength::MESSAGE,
    IncorrectParsing::CODE => IncorrectParsing::MESSAGE,
    IncorrectPattern::CODE => IncorrectPattern::MESSAGE,
    ExceedingMaxRange::CODE => ExceedingMaxRange::MESSAGE,
    ExceedingMinRange::CODE => ExceedingMinRange::MESSAGE
]);

define('ErrorDetails', [
    Success::CODE => Success::DETAILS,
    NullAttributes::CODE => NullAttributes::DETAILS,
    ExceedingMaxLength::CODE => ExceedingMaxLength::DETAILS,
    ExceedingMinLength::CODE => ExceedingMinLength::DETAILS,
    IncorrectParsing::CODE => IncorrectParsing::DETAILS,
    IncorrectPattern::CODE => IncorrectPattern::DETAILS,
    ExceedingMaxRange::CODE => ExceedingMaxRange::DETAILS,
    ExceedingMinRange::CODE => ExceedingMinRange::DETAILS
]);

// ==== success case =====================================================================

interface Success
{
    const CODE = 00;
    const MESSAGE = 'success';
    const DETAILS = 'action completed';
}

// ==== generic invalid cases ============================================================

interface NullAttributes
{
    const CODE = 10;
    const MESSAGE = "attribute can't be null";
    const DETAILS = 'the attribute does not exist or is null';
}

// ==== string invalid cases =============================================================

interface ExceedingMaxLength
{
    const CODE = 20;
    const MESSAGE = "string exceed the maximum length";
    const DETAILS = 'the string-typed attribute exceeds the maximum permitted length';
}

interface ExceedingMinLength
{
    const CODE = 21;
    const MESSAGE = "string exceed the minimum length";
    const DETAILS = 'the string-typed attribute exceeds the minimum permitted length';
}

interface IncorrectParsing
{
    const CODE = 22;
    const MESSAGE = "string isn't one of the predefined";
    const DETAILS = "the string-typed attribute doesn't correspond to predefined schema";
}

interface IncorrectPattern
{
    const CODE = 23;
    const MESSAGE = "string isn't following the regex pattern";
    const DETAILS = "the string-typed attribute doesn't follow the regex pattern";
}

// ==== integer (32 bit) invalid cases ===================================================

interface ExceedingMaxRange
{
    const CODE = 30;
    const MESSAGE = "integer exceed the maximum value";
    const DETAILS = 'the int-typed attribute exceeds the maximum permitted value';
}

interface ExceedingMinRange
{
    const CODE = 31;
    const MESSAGE = "integer exceed the minimum value";
    const DETAILS = 'the int-typed attribute exceeds the minimum permitted value';
}