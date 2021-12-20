<?php

namespace Model\Transaction;

use Exception;

/**
 * Transaction Interface, declare specific methods
 * for the transaction resource
 *
 * @category Interface
 */
interface Transaction
{
    /**
     * Get function for the database identifier
     * of the transaction
     *
     * @return integer
     */
    public function getId(): int;

    /**
     * Get function for the database identifier
     * of the deposit linked to the transaction
     *
     * @return int
     */
    public function getDepositId(): int;

    /**
     * Get function for the type of the transaction
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set function for the type of the transaction
     * Checks the constrains before assignation
     *
     * @param string $type
     * @return Transaction
     * @throws Exception
     */
    public function setType(string $type): Transaction;

    /**
     * Get function for the amount of the transaction
     *
     * @return int
     */
    public function getAmount(): int;

    /**
     * Set function for the amount of the transaction
     * Checks the constrains before assignation
     *
     * @param int $amount
     * @return Transaction
     * @throws Exception
     */
    public function setAmount(int $amount): Transaction;

    /**
     * Get function for the moment of the transaction
     *
     * @return string
     */
    public function getTimestamp(): string;

    /**
     * Set function for the moment of the transaction
     * Checks the constrains before assignation
     *
     * @param string $timestamp
     * @return Transaction
     * @throws Exception
     */
    public function setTimestamp(string $timestamp): Transaction;

    /**
     * Get function for the author of the transaction
     *
     * @return string
     */
    public function getAuthor(): string;

    /**
     * Set function for the author of the transaction
     * Checks the constrains before assignation
     *
     * @param string $author
     * @return Transaction
     * @throws Exception
     */
    public function setAuthor(string $author): Transaction;
}