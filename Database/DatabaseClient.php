<?php

namespace Database;

use Exception;
use PgSql\Connection;

class DatabaseClient
{
    public const TABLE_NAME_BOOKS = 'interpay.books';
    public const TABLE_NAME_AUTHORS = 'interpay.authors';

    private static DatabaseClient $client;
    private Connection $connection;

    private function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $host
     * @param string $port
     * @param string $dbName
     * @param string $user
     * @param string $password
     *
     * @return self
     *
     * @throws Exception
     */
    public static function getInstance(
        string $host,
        string $port,
        string $dbName,
        string $user,
        string $password
    ): self {
        if (!empty(self::$client)) {
            return self::$client;
        }

        $connectionString = sprintf(
            'host=%s port=%s dbname=%s user=%s password=%s',
            $host,
            $port,
            $dbName,
            $user,
            $password
        );
        $connection = pg_connect($connectionString);

        if (!$connection) {
            throw new Exception('Error in database connection!');
        }

        self::$client = new self($connection);

        return self::$client;
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @param array $values
     *
     * @return bool
     *
     * @throws Exception
     */
    public function insert(string $tableName, array $columns, array $values): bool
    {
        if (empty($columns)) {
            throw new Exception('You should provide at least one column!');
        }

        if (count($columns) !== count($values)) {
            throw new Exception('You should provide equal numbers of column and values!');
        }

        $statement = $this->prepareInsertStatement($tableName, $columns, $values);

        $result = pg_query($this->connection, $statement);

        if (!$result) {
            throw new Exception(pg_last_error($this->connection));
        }

        return true;
    }

    /**
     * @param array $criteria
     *
     * @return array
     *
     * @throws Exception
     */
    public function searchBooksByAuthor(array $criteria): array
    {
        $statement = $this->prepareSelectStatement($criteria);

        $result = pg_query($this->connection, $statement);

        if (!$result) {
            throw new Exception(pg_last_error($this->connection));
        }

        $rows = pg_num_rows($result);
        if ($rows === 0) {
            return [];
        }

        $resultsArray = [];
        for ($i = 0; $i < $rows; $i++) {
            $resultsArray[] = [
                'id' => pg_fetch_result($result, $i, 0),
                'title' => pg_fetch_result($result, $i, 1),
                'authorId' => pg_fetch_result($result, $i, 2),
            ];
        }

        return $resultsArray;
    }

    /**
     * @param array $criteria
     *
     * @return array
     *
     * @throws Exception
     */
    public function getAuthor(array $criteria): array
    {
        $whereStatement = [];
        foreach ($criteria as $column => $value) {
            if (is_string($value)) {
                $value = '\'' . $value . '\'';
            }

            $whereStatement[] = "$column = $value";
        }

        $query = sprintf("SELECT * FROM %s WHERE %s", self::TABLE_NAME_AUTHORS, implode(' AND ', $whereStatement));

        $result = pg_query($this->connection, $query);

        if (!$result) {
            return [];
        }

        $rows = pg_num_rows($result);
        if ($rows === 0) {
            return [];
        }

        return [
            'id' => pg_fetch_result($result, 0, 0),
            'name' => pg_fetch_result($result, 0, 1),
        ];
    }

    /**
     * @param array $criteria
     *
     * @return array
     */
    public function getBook(array $criteria): array
    {
        $whereStatement = [];
        foreach ($criteria as $column => $value) {
            if (is_string($value)) {
                $value = '\'' . $value . '\'';
            }

            $whereStatement[] = "$column = $value";
        }

        $query = sprintf("SELECT * FROM %s WHERE %s", self::TABLE_NAME_BOOKS, implode(' AND ', $whereStatement));

        $result = pg_query($this->connection, $query);

        if (!$result) {
            return [];
        }

        $rows = pg_num_rows($result);
        if ($rows === 0) {
            return [];
        }

        return [
            'id' => pg_fetch_result($result, 0, 0),
            'title' => pg_fetch_result($result, 0, 1),
            'authorId' => pg_fetch_result($result, 0, 2),
        ];
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @param array $values
     *
     * @return string
     */
    private function prepareInsertStatement(string $tableName, array $columns, array $values): string
    {
        $values = array_map(function($value) {
            if (is_string($value)) {
                return '\'' . $value . '\'';
            }

            return $value;
        }, $values);

        return sprintf("INSERT INTO %s (%s) VALUES (%s)", $tableName, implode(',', $columns), implode(',', $values));
    }

    /**
     * @param array $criteria
     *
     * @return string
     */
    private function prepareSelectStatement(array $criteria): string
    {
        $whereStatement = [];
        foreach ($criteria as $column => $value) {
            if (is_string($value)) {
                $value = '\'' . $value . '\'';
            }

            $whereStatement[] = "$column = $value";
        }

        $joinCriteria = self::TABLE_NAME_AUTHORS . '.id = ' . self::TABLE_NAME_BOOKS . '.author_id';

        $fields = [
            self::TABLE_NAME_BOOKS . '.id',
            self::TABLE_NAME_BOOKS . '.title',
            self::TABLE_NAME_BOOKS . '.author_id'
        ];

        return sprintf(
            "SELECT %s FROM %s INNER JOIN %s on %s WHERE %s;",
            implode(',', $fields),
            self::TABLE_NAME_BOOKS,
            self::TABLE_NAME_AUTHORS,
            $joinCriteria,
            implode(' AND ', $whereStatement)
        );
    }
}