<?php

namespace Services\Database;

use Exception;
use PDO;
use PDOStatement;
use Specifications\Database\Database;

class Module
{
    private PDO $db;

    /**
     * Initialize the database connection
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->db = new PDO(
            "mysql:host=" . Database::SERVER_NAME . ";dbname=" . Database::DATABASE_NAME,
            Database::DATABASE_USER,
            Database::DATABASE_USER_PASSWORD
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Begin the transaction on the private db reference
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit the transaction on the private db reference
     *
     * @return bool
     */
    public function commitTransaction(): bool
    {
        return $this->db->commit();
    }

    /**
     * Execute the query passed as parameters
     * with the optional parameters as array
     * and return the statement ready to be fetched
     *
     * @param string $query the query to execute
     * @param array|null $params the optional parameters
     * @return bool|PDOStatement the statement
     */
    private function executeQuery(string $query, array $params = null): bool|PDOStatement
    {
        $statement = $this->db->prepare($query);

        if ($params != null) {
            foreach ($params as $reference => &$value) {
                $statement->bindParam($reference, $value);
            }
        }

        $statement->execute();

        return $statement;
    }

    /**
     * Execute the query passed as parameters
     * with the optional parameters as array
     * and return the first row fetched
     *
     * @param string $query the query to execute
     * @param array|null $params the optional parameters
     * @return array|false the set of attributes in the row, false in case of empty
     */
    public function fetchOne(string $query, array $params = null): array|false
    {
        $statement = $this->executeQuery($query, $params);

        return $statement->fetch();
    }

    /**
     * Execute the query passed as parameters
     * with the optional parameters as array
     * and return all the rows fetched
     *
     * @param string $query the query to execute
     * @param array|null $params the optional parameters
     * @return array|false the list of row, false in case of empty
     */
    public function fetchAll(string $query, array $params = null): array|false
    {
        $statement = $this->executeQuery($query, $params);

        return $statement->fetchAll();
    }

    /**
     * Execute the query passed as parameters
     * with the optional parameters as array
     * and doesn't return the result
     *
     * @param string $query the query to execute
     * @param array|null $params the optional parameters
     */
    public function execute(string $query, array $params = null): void
    {
        $this->executeQuery($query, $params);
    }
}