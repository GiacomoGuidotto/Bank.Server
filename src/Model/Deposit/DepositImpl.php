<?php

namespace Model\Deposit;

use Exception;
use Model\Entity;
use Specifics\ErrorCases\ExceedingMaxLength;
use Specifics\ErrorCases\ExceedingMaxRange;
use Specifics\ErrorCases\ExceedingMinLength;
use Specifics\ErrorCases\IncorrectParsing;
use Specifics\ErrorCases\NullAttributes;

class DepositImpl implements Entity, Deposit
{
    private static string $entityName = 'deposit';

    private static array $attributesAndVisibility = [
        'id' => self::PRIVATE_VISIBILITY,
        'userId' => self::PRIVATE_VISIBILITY,
        'name' => self::PUBLIC_VISIBILITY,
        'amount' => self::PUBLIC_VISIBILITY,
        'type' => self::PUBLIC_VISIBILITY,
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
    private int $userId;
    private string $name;
    private int $amount;
    private string $type;
    private bool $active;

    /**
     * @throws Exception
     */
    public function __construct(
        int    $id,
        int    $userId,
        string $name,
        int    $amount,
        string $type,
        bool   $active
    )
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->name = $name;
        $this->amount = $amount;
        $this->type = $type;
        $this->active = $active;

        $this->validate();

    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $this->validateId($this->id);
        $this->validateUserId($this->userId);
        $this->validateName($this->name);
        $this->validateAmount($this->amount);
        $this->validateType($this->type);
        $this->validateActive($this->active);
    }

    // ==== Object specific methods ============================================

    /**
     * Checks the constrains of the id attribute
     *
     * @throws Exception
     */
    private function validateId(int $id)
    {
        if ($id == null) {
            throw new Exception(
                '[id]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE
            );
        }
    }

    /**
     * Checks the constrains of the userId attribute
     *
     * @throws Exception
     */
    private function validateUserId(int $userId)
    {
        if ($userId == null) {
            throw new Exception(
                '[userId]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE
            );
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
            throw new Exception(
                '[name]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE);
        }
        if (strlen($name) > 64) {
            throw new Exception(
                '[name]: ' . ExceedingMaxLength::MESSAGE,
                ExceedingMaxLength::CODE);
        }
        if (strlen($name) < 1) {
            throw new Exception('[name]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
        }
    }

    /**
     * Checks the constrains of the amount attribute
     *
     * @throws Exception
     */
    private function validateAmount(int $amount)
    {
        if ($amount == null) {
            throw new Exception(
                '[amount]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE);
        }
        if ($amount > 2 ** 31 - 1) {
            throw new Exception(
                '[amount]: ' . ExceedingMaxRange::MESSAGE,
                ExceedingMaxRange::CODE);
        }
    }

    /**
     * Checks the constrains of the type attribute
     *
     * @throws Exception
     */
    private function validateType(string $type)
    {
        $enum = ['standard'];

        if ($type == null) {
            throw new Exception(
                '[type]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE);
        }
        if (!in_array($type, $enum, true)) {
            throw new Exception(
                '[type]: ' . IncorrectParsing::MESSAGE,
                IncorrectParsing::CODE);
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
            throw new Exception(
                '[active]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE);
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
    public function getUserId(): int
    {
        return $this->userId;
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
    public function setName(string $name): Deposit
    {
        $this->validateName($name);
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @inheritDoc
     */
    public function setAmount(int $amount): Deposit
    {
        $this->validateAmount($amount);
        $this->amount = $amount;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): Deposit
    {
        $this->validateType($type);
        $this->type = $type;
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
    public function setActive(bool $active): Deposit
    {
        $this->validateActive($active);
        $this->active = $active;
        return $this;
    }
}