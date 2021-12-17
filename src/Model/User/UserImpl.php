<?php

namespace Model\User;

use Exception;
use Model\Entity;
use Specifics\ErrorCases\ExceedingMaxLength;
use Specifics\ErrorCases\ExceedingMinLength;
use Specifics\ErrorCases\NullAttributes;

/**
 * UserImpl Implementation, the representation of the user resource
 * through the general Object Model Interface
 * and the UserImpl Interface
 *
 * @category Class
 */
class UserImpl implements Entity, User
{
    private static string $entityName = 'user';

    private static array $attributesAndVisibility = [
        'id' => self::PRIVATE_VISIBILITY,
        'username' => self::PUBLIC_VISIBILITY,
        'password' => self::PRIVATE_VISIBILITY,
        'name' => self::PUBLIC_VISIBILITY,
        'surname' => self::PUBLIC_VISIBILITY,
        'IBAN' => self::PUBLIC_VISIBILITY,
        'active' => self::PRIVATE_VISIBILITY
    ];

    /**
     * @inheritDoc
     */
    public function getEntityName(): string
    {
        return self::$entityName;
    }

    /**
     * @inheritDoc
     */
    public function getAttributesAndVisibility(): array
    {
        return self::$attributesAndVisibility;
    }

    private int $id;
    private string $username;
    private string $password;
    private string $name;
    private string $surname;
    private string $IBAN;
    private bool $active;

    /**
     * @throws Exception
     */
    public function __construct(
        int    $id,
        string $username,
        string $password,
        string $name,
        string $surname,
        string $IBAN,
        bool   $active
    )
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->IBAN = $IBAN;
        $this->active = $active;

        $this->validate();
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $this->validateId($this->id);
        $this->validateUsername($this->username);
        $this->validatePassword($this->password);
        $this->validateName($this->name);
        $this->validateSurname($this->surname);
        $this->validateIBAN($this->IBAN);
        $this->validateActive($this->active);
    }

    // ==== Object specifics methods ===========================================

    /**
     * Checks the constrains of the id attribute
     *
     * @throws Exception
     */
    private function validateId(int $id)
    {
        if ($id == null) {
            throw new Exception('[id]: ' . NullAttributes::MESSAGE, NullAttributes::CODE);
        }
    }

    /**
     * Checks the constrains of the username attribute
     *
     * @throws Exception
     */
    private function validateUsername(string $username)
    {
        if ($username == null) {
            throw new Exception('[username]: ' . NullAttributes::MESSAGE, NullAttributes::CODE);
        }
        if (strlen($username) > 64) {
            throw new Exception('[username]: ' . ExceedingMaxLength::MESSAGE, ExceedingMaxLength::CODE);
        }
        if (strlen($username) < 1) {
            throw new Exception('[username]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
        }
    }

    /**
     * Checks the constrains of the password attribute
     *
     * @throws Exception
     */
    private function validatePassword(string $password)
    {
        if ($password == null) {
            throw new Exception('[password]: ' . NullAttributes::MESSAGE, NullAttributes::CODE);
        }
        if (strlen($password) > 32) {
            throw new Exception('[password]: ' . ExceedingMaxLength::MESSAGE, ExceedingMaxLength::CODE);
        }
        if (strlen($password) < 8) {
            throw new Exception('[password]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
        }
    }

    /**
     * Checks the constrains of the name attribute
     *
     * @throws Exception
     */
    private function validateName(string $name)
    {
        if ($name == null) {
            throw new Exception('[name]: ' . NullAttributes::MESSAGE, NullAttributes::CODE);
        }
        if (strlen($name) > 64) {
            throw new Exception('[name]: ' . ExceedingMaxLength::MESSAGE, ExceedingMaxLength::CODE);
        }
        if (strlen($name) < 1) {
            throw new Exception('[name]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
        }
    }

    /**
     * Checks the constrains of the surname attribute
     *
     * @throws Exception
     */
    private function validateSurname(string $surname)
    {
        if ($surname == null) {
            throw new Exception('[surname]: ' . NullAttributes::MESSAGE, NullAttributes::CODE);
        }
        if (strlen($surname) > 64) {
            throw new Exception('[surname]: ' . ExceedingMaxLength::MESSAGE, ExceedingMaxLength::CODE);
        }
        if (strlen($surname) < 1) {
            throw new Exception('[surname]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
        }
    }

    /**
     * Checks the constrains of the IBAN attribute
     *
     * @throws Exception
     */
    private function validateIBAN(string $IBAN)
    {
        // IBAN checks
        if ($IBAN == null) {
            throw new Exception('[IBAN]: ' . NullAttributes::MESSAGE, NullAttributes::CODE);
        }
        if (strlen($IBAN) > 32) {
            throw new Exception('[IBAN]: ' . ExceedingMaxLength::MESSAGE, ExceedingMaxLength::CODE);
        }
        if (strlen($IBAN) < 15) {
            throw new Exception('[IBAN]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
        }
    }

    /**
     * Checks the constrains of the active attribute
     *
     * @throws Exception
     */
    private function validateActive(bool $active)
    {
        if ($active == null) {
            throw new Exception('[active]: ' . NullAttributes::MESSAGE, NullAttributes::CODE);
        }
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setUsername(string $username): User
    {
        $this->validateUsername($username);
        $this->username = $username;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function setPassword(string $password): User
    {
        $this->validatePassword($password);
        $this->password = $password;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): User
    {
        $this->validateName($name);
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSurname(): string
    {
        return $this->surname;
    }


    /**
     * @inheritDoc
     */
    public function setSurname(string $surname): User
    {
        $this->validateSurname($surname);
        $this->surname = $surname;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIBAN(): string
    {
        return $this->IBAN;
    }

    /**
     * @inheritDoc
     */
    public function setIBAN(string $IBAN): User
    {
        $this->validateIBAN($IBAN);
        $this->IBAN = $IBAN;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @inheritDoc
     */
    public function setActive(bool $active): User
    {
        $this->validateActive($active);
        $this->active = $active;
        return $this;
    }
}