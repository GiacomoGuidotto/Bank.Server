<?php

namespace Services\Database;

use Exception;
use PDO;
use Specifics\Database\DatabaseSpecifics;

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
            "mysql:host=" . DatabaseSpecifics::SERVER_NAME . ";dbname=" . DatabaseSpecifics::DATABASE_NAME,
            DatabaseSpecifics::DATABASE_USER,
            DatabaseSpecifics::DATABASE_USER_PASSWORD
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
     * of keys (the reference in the query) and values (its real value)
     *
     * @param string $query
     * @param array|null $params
     * @return void
     */
    public function executeQuery(string $query, array $params = null)
    {
        $statement = $this->db->prepare($query);

        if ($params != null) {
            foreach ($params as $reference => $value) {
                $statement->bindParam($reference, $value);
            }
        }

        $statement->execute();
    }
}