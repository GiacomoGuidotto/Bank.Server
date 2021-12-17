<?php

namespace Model\User;

use Exception;

/**
 * UserImpl Interface, declare specific methods for the user resource
 *
 * @category Interface
 */
interface User
{
    /**
     * Get function for the database identifier of the user
     *
     * @return integer
     */
    public function getId(): int;

    /**
     * Get function for the username of the user
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Set function for the username of the user
     * Checks the constrains before assignation
     *
     * @param string $username
     * @return self
     * @throws Exception
     */
    public function setUsername(string $username): User;

    /**
     * Get function for the password of the user
     *
     * @return string
     */
    public function getPassword(): string;

    /**
     * Set function for the password of the user
     * Checks the constrains before assignation
     *
     * @param string $password
     * @return self
     * @throws Exception
     */
    public function setPassword(string $password): User;

    /**
     * Get function for the name of the user
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set function for the name of the user
     * Checks the constrains before assignation
     *
     * @param string $name
     * @return self
     * @throws Exception
     */
    public function setName(string $name): User;

    /**
     * Get function for the surname of the user
     *
     * @return string
     */
    public function getSurname(): string;

    /**
     * Set function for the surname of the user
     * Checks the constrains before assignation
     *
     * @param string $surname
     * @return self
     * @throws Exception
     */
    public function setSurname(string $surname): User;

    /**
     * Get function for the IBAN of the user
     *
     * @return string
     */
    public function getIBAN(): string;

    /**
     * Set function for the IBAN of the user
     * Checks the constrains before assignation
     *
     * @param string $IBAN
     * @return self
     * @throws Exception
     */
    public function setIBAN(string $IBAN): User;

    /**
     * Get function for the active state of the user
     *
     * @return boolean
     */
    public function getActive(): bool;

    /**
     * Set function for the active state of the user
     * Checks the constrains before assignation
     *
     * @param bool $active
     * @return self
     * @throws Exception
     */
    public function setActive(bool $active): User;
}