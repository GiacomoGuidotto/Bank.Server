<?php

namespace Model;

use Exception;

interface Entity
{
    /**
     * The name of the Object.
     *
     * @return string
     */
    public function getEntityName(): string;

    /**
     * Return the list of attributes names as keys
     * associated to their visibility as values
     *
     * @return array
     */
    public function getAttributesAndVisibility(): array;

    /**
     * VISIBILITY options
     */
    public const PUBLIC_VISIBILITY = 'public';
    public const PRIVATE_VISIBILITY = 'private';


    /**
     * Checks the application of the constrains
     * for each attributes
     * with the help of private class methods
     *
     * @throws Exception
     */
    public function validate();
}