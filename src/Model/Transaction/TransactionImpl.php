<?php

namespace Model\Transaction;

use Exception;
use Model\Entity;
use Specifics\ErrorCases\ExceedingMaxLength;
use Specifics\ErrorCases\ExceedingMaxRange;
use Specifics\ErrorCases\ExceedingMinLength;
use Specifics\ErrorCases\IncorrectParsing;
use Specifics\ErrorCases\IncorrectPattern;
use Specifics\ErrorCases\NullAttributes;

class TransactionImpl implements Entity, Transaction
{
    private static string $entityName = 'transaction';

    private static array $attributesAndVisibility = [
        'id' => self::PRIVATE_VISIBILITY,
        'depositId' => self::PRIVATE_VISIBILITY,
        'type' => self::PUBLIC_VISIBILITY,
        'amount' => self::PUBLIC_VISIBILITY,
        'timestamp' => self::PUBLIC_VISIBILITY,
        'author' => self::PUBLIC_VISIBILITY,
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
    private int $depositId;
    private string $type;
    private int $amount;
    private string $timestamp;
    private string $author;

    /**
     * @throws Exception
     */
    public function __construct(
        int    $id,
        int    $depositId,
        string $type,
        int    $amount,
        string $timestamp,
        string $author,
    )
    {
        $this->id = $id;
        $this->depositId = $depositId;
        $this->type = $type;
        $this->amount = $amount;
        $this->timestamp = $timestamp;
        $this->author = $author;

        $this->validate();
    }


    /**
     * @inheritDoc
     */
    public function validate()
    {
        $this->validateId($this->id);
        $this->validateDepositId($this->depositId);
        $this->validateType($this->type);
        $this->validateAmount($this->amount);
        $this->validateTimestamp($this->timestamp);
        $this->validateAuthor($this->author);
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
     * Checks the constrains of the depositId attribute
     *
     * @throws Exception
     */
    private function validateDepositId(int $depositId)
    {
        if ($depositId == null) {
            throw new Exception(
                '[depositId]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE
            );
        }
    }

    /**
     * Checks the constrains of the type attribute
     *
     * @throws Exception
     */
    private function validateType(string $type)
    {
        $enum = ['withdraw', 'deposit'];

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
     * Checks the constrains of the timestamp attribute
     *
     * @throws Exception
     */
    private function validateTimestamp(int $timestamp)
    {
        if ($timestamp == null) {
            throw new Exception(
                '[timestamp]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE);
        }
        if (strlen($timestamp) > 19) {
            throw new Exception('[password]: ' . ExceedingMaxLength::MESSAGE, ExceedingMaxLength::CODE);
        }
        if (strlen($timestamp) < 19) {
            throw new Exception('[password]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
        }
//      // e.g. 2021-12-25 12:00:00
        if (preg_match("#([0-9]{4})-(0[1-9]|1[1|2])-([0-2][0-9]|3[0|1]) ([0|1][0-9]|2[0-3])(:[0-5][0-9]){2}#", $timestamp) != 1) {
            throw new Exception('[password]: ' . IncorrectPattern::MESSAGE, IncorrectPattern::CODE);
        }
    }

    /**
     * Checks the constrains of the author attribute
     *
     * @throws Exception
     */
    private function validateAuthor(string $author)
    {
        if ($author == null) {
            throw new Exception(
                '[name]: ' . NullAttributes::MESSAGE,
                NullAttributes::CODE);
        }
        if (strlen($author) > 129) {
            throw new Exception(
                '[name]: ' . ExceedingMaxLength::MESSAGE,
                ExceedingMaxLength::CODE);
        }
        if (strlen($author) < 1) {
            throw new Exception('[name]: ' . ExceedingMinLength::MESSAGE, ExceedingMinLength::CODE);
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
    public function getDepositId(): int
    {
        return $this->depositId;
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
    public function setType(string $type): Transaction
    {
        $this->validateType($type);
        $this->type = $type;
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
    public function setAmount(int $amount): Transaction
    {
        $this->validateAmount($amount);
        $this->amount = $amount;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @inheritDoc
     */
    public function setTimestamp(string $timestamp): Transaction
    {
        $this->validateTimestamp($timestamp);
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @inheritDoc
     */
    public function setAuthor(string $author): Transaction
    {
        $this->validateAuthor($author);
        $this->author = $author;
        return $this;
    }
}