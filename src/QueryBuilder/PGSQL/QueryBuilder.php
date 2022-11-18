<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\PGSQL;

use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\PGSQL\Statements\Insert;
use DataMapper\QueryBuilder\QueryBuilder as ParentQueryBuilder;
use PDO;

/**
 * Class QueryBuilder
 *
 * @package DataMapper\QueryBuilder\PGSQL
 */
final class QueryBuilder extends ParentQueryBuilder
{
    /**
     * QueryBuilder constructor.
     *
     * @param PDO $pdo
     *
     */
    protected function __construct(private PDO $pdo)
    {
    }

    /**
     * @param string $table
     * @param array<mixed> $values
     *
     * @return int
     * @throws Exception
     */
    public function insert(string $table, array $values): int
    {
        $insertStatement = new Insert($table, $values);
        $statement = $this->pdo->query((string)$insertStatement);

        if (!$statement) {
            throw new Exception('Invalid query ' . $insertStatement);
        }

        foreach ($values as $value) {
            $statement->bindParam($value['key'], $value['value'], $this->getType($value['type']));
        }

        if (!$result = $statement->execute()) {
            throw new Exception('Invalid query ' . $insertStatement);
        }

        return $statement->fetch();
    }
}
