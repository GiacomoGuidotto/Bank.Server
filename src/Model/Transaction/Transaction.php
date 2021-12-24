<?php

namespace Model\Transaction;

/**
 * Transaction Interface, declare specific methods
 * for the transaction resource
 *
 * @category Interface
 */
interface Transaction
{
    /**
     * Checks the constrains of the transaction's type attribute
     *
     * @param string $type the type to check
     * @return int         either the error code
     *                     or the success code
     */
    public static function validateType(string $type): int;

    /**
     * Checks the constrains of the transaction's amount attribute
     *
     * @param int $amount the amount to check
     * @return int        either the error code
     *                    or the success code
     */
    public static function validateAmount(int $amount): int;

    /**
     * Checks the constrains of the transaction's timestamp attribute
     *
     * @param string $timestamp the amount to check
     * @return int              either the error code
     *                          or the success code
     */
    public static function validateTimestamp(string $timestamp): int;

    /**
     * Checks the constrains of the transaction's author attribute
     *
     * @param string $author the author to check
     * @return int           either the error code
     *                       or the success code
     */
    public static function validateAuthor(string $author): int;
}