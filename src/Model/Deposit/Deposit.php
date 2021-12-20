<?php

namespace Model\Deposit;

use Exception;

/**
 * Deposit Interface, declare specific methods for the deposit resource
 *
 * @category Interface
 */
interface Deposit
{
    /**
     * Get function for the database identifier
     * of the deposit
     *
     * @return integer
     */
    public function getId(): int;

    /**
     * Get function for the database identifier
     * of the user linked to the deposit
     *
     * @return integer
     */
    public function getUserId(): int;

    /**
     * Get function for the name of the deposit
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set function for the name of the deposit
     * Checks the constrains before assignation
     *
     * @param string $name
     * @return self
     * @throws Exception
     */
    public function setName(string $name): Deposit;

    /**
     * Get function for the amount of the deposit
     *
     * @return int
     */
    public function getAmount(): int;

    /**
     * Set function for the amount of the deposit
     * Checks the constrains before assignation
     *
     * @param int $amount
     * @return self
     * @throws Exception
     */
    public function setAmount(int $amount): self;

    /**
     * Get function for the type of the deposit
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set function for the type of the deposit
     * Checks the constrains before assignation
     *
     * @param string $type
     * @return self
     * @throws Exception
     */
    public function setType(string $type): Deposit;

    /**
     * Get function for the type of the deposit
     *
     * @return boolean
     */
    public function getActive(): bool;

    /**
     * Set function for the type of the deposit
     * Checks the constrains before assignation
     *
     * @param boolean $active
     * @return self
     * @throws Exception
     */
    public function setActive(bool $active): self;

}