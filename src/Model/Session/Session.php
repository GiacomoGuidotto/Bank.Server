<?php

namespace Model\Session;

interface Session
{
    /**
     * Checks the constrains of the session token
     *
     * @param string $token the token to check
     * @return int          either the error code
     *                      or the success code
     */
    public static function validateToken(string $token): int;

    /**
     * Checks the constrains of the timestamp of creation
     *
     * @param string $timestamp the time to check
     * @return int              either the error code
     *                          or the success code
     */
    public static function validateCreationTimestamp(string $timestamp): int;

    /**
     * Checks the constrains of the timestamp of the last update
     *
     * @param string $timestamp the time to check
     * @return int              either the error code
     *                          or the success code
     */
    public static function validateLastUpdated(string $timestamp): int;
}