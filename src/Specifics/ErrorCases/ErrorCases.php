<?php
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Specifics\ErrorCases;

define('ErrorCodes', [
    // success
    00 => 'Success',
    // generic
    10 => 'NullAttributes',
    // string-related
    20 => 'ExceedingMaxLength',
    21 => 'ExceedingMinLength',
    22 => 'IncorrectParsing',
    23 => 'IncorrectPattern',
    // int-related
    30 => 'ExceedingMaxRange',
    31 => 'ExceedingMinRange'
]);

interface Success
{
    const CODE = 00;
    const MESSAGE = 'success';
    const DETAILS = 'action completed';
}

// generic invalid cases

interface NullAttributes
{
    const CODE = 10;
    const MESSAGE = "attribute can't be null";
    const DETAILS = 'the attribute does not exist or is null';
}

// string invalid cases

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

// integer (32 bit) invalid cases

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