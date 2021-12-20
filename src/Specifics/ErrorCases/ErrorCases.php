<?php
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Specifics\ErrorCases;

define('ErrorCodes', [
    // generic
    00 => 'NullAttributes',
    // string-related
    10 => 'ExceedingMaxLength',
    11 => 'ExceedingMinLength',
    12 => 'IncorrectParsing',
    13 => 'IncorrectPattern',
    // int-related
    20 => 'ExceedingMaxRange',
    21 => 'ExceedingMinRange'
]);

// generic invalid cases

class NullAttributes
{
    const CODE = 00;
    const MESSAGE = "attribute can't be null";
    const DETAILS = 'the attribute does not exist or is null';
}

// string invalid cases

class ExceedingMaxLength
{
    const CODE = 10;
    const MESSAGE = "string exceed the maximum length";
    const DETAILS = 'the string-typed attribute exceeds the maximum permitted length';
}

class ExceedingMinLength
{
    const CODE = 11;
    const MESSAGE = "string exceed the minimum length";
    const DETAILS = 'the string-typed attribute exceeds the minimum permitted length';
}

class IncorrectParsing
{
    const CODE = 12;
    const MESSAGE = "string isn't one of the predefined";
    const DETAILS = "the string-typed attribute doesn't correspond to predefined schema";
}

class IncorrectPattern
{
    const CODE = 13;
    const MESSAGE = "string isn't following the regex pattern";
    const DETAILS = "the string-typed attribute doesn't follow the regex pattern";
}

// integer (32 bit) invalid cases

class ExceedingMaxRange
{
    const CODE = 20;
    const MESSAGE = "integer exceed the maximum value";
    const DETAILS = 'the int-typed attribute exceeds the maximum permitted value';
}

class ExceedingMinRange
{
    const CODE = 21;
    const MESSAGE = "integer exceed the minimum value";
    const DETAILS = 'the int-typed attribute exceeds the minimum permitted value';
}