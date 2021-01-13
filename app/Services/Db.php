<?php

namespace Services;

class Db
{
    /** @var \PDO */
    private $pdo;

    private static $instance;

    private function __construct()
    {
        $this->pdo = new \PDO('sqlite:../../database/cian-parser.db');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return Db
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $className
     * @return array|null
     */
    public function query(string $sql, $params = [], string $className = 'stdClass'): ?array
    {
        try {
            $sth = $this->pdo->prepare($sql);
        } catch (\PDOException $exception) {
            var_dump($exception); // @todo throw exceptions
            return null;
        }

        $result = $sth->execute($params);

        if (false === $result) {
            return null;
        }

        return $sth->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    /**
     * @return int
     */
    public function getLastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}