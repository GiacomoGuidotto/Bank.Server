<?php
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Specifications\ErrorCases;

/**
 * Error cases schemas:
 * code, message, details
 *
 * ==== success case ===========
 * 00 Success
 *
 * ==== generic ================
 * 10 Null attributes
 *
 * ==== string-related =========
 * 20 Exceeding max length
 * 21 Exceeding min length
 * 22 Incorrect parsing
 * 23 Incorrect pattern
 *
 * ==== int-related ============
 * 30 Exceeding max range
 * 31 Exceeding min range
 *
 * ==== elaboration-related ====
 * 40 Already exist
 * 41 Not found
 * 42 Unauthorized
 * 43 Timeout
 * 44 Bad parameters
 */
interface ErrorCases
{
    const ERROR_MESSAGES = [
        Success::CODE => Success::MESSAGE,
        NullAttributes::CODE => NullAttributes::MESSAGE,
        ExceedingMaxLength::CODE => ExceedingMaxLength::MESSAGE,
        ExceedingMinLength::CODE => ExceedingMinLength::MESSAGE,
        IncorrectParsing::CODE => IncorrectParsing::MESSAGE,
        IncorrectPattern::CODE => IncorrectPattern::MESSAGE,
        ExceedingMaxRange::CODE => ExceedingMaxRange::MESSAGE,
        ExceedingMinRange::CODE => ExceedingMinRange::MESSAGE,
        AlreadyExist::CODE => AlreadyExist::MESSAGE,
        NotFound::CODE => NotFound::MESSAGE,
        Unauthorized::CODE => Unauthorized::MESSAGE,
        Timeout::CODE => Timeout::MESSAGE,
        BadParameters::CODE => BadParameters::MESSAGE
    ];
    const ERROR_DETAILS = [
        Success::CODE => Success::DETAILS,
        NullAttributes::CODE => NullAttributes::DETAILS,
        ExceedingMaxLength::CODE => ExceedingMaxLength::DETAILS,
        ExceedingMinLength::CODE => ExceedingMinLength::DETAILS,
        IncorrectParsing::CODE => IncorrectParsing::DETAILS,
        IncorrectPattern::CODE => IncorrectPattern::DETAILS,
        ExceedingMaxRange::CODE => ExceedingMaxRange::DETAILS,
        ExceedingMinRange::CODE => ExceedingMinRange::DETAILS,
        AlreadyExist::CODE => AlreadyExist::DETAILS,
        NotFound::CODE => NotFound::DETAILS,
        Unauthorized::CODE => Unauthorized::DETAILS,
        Timeout::CODE => Timeout::DETAILS,
        BadParameters::CODE => BadParameters::DETAILS
    ];
}


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

// ==== elaboration invalid cases ========================================================

interface AlreadyExist
{
    const CODE = 40;
    const MESSAGE = "the entity already exist";
    const DETAILS = 'the entity attributes already have this values';
}

interface NotFound
{
    const CODE = 41;
    const MESSAGE = "the entity does not exist";
    const DETAILS = "the elaboration parameters didn't produced any entity";
}

interface Unauthorized
{
    const CODE = 42;
    const MESSAGE = "the session token does not exist";
    const DETAILS = "the session token served doesn't exist, impossible to confirm authority";
}

interface Timeout
{
    const CODE = 43;
    const MESSAGE = "the session has expired";
    const DETAILS = "the time to live of the session token ended";
}

interface BadParameters
{
    const CODE = 44;
    const MESSAGE = "the parameters are not valid";
    const DETAILS = "the given parameters are either NULL or generally invalid";
}