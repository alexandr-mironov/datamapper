<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\PGSQL;

use DataMapper\Entity\FieldCollection;
use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\PGSQL\Statements\Insert;
use DataMapper\QueryBuilder\QueryBuilder as ParentQueryBuilder;
use PDO;

/**
 * Class QueryBuilder
 *
 * @package DataMapper\QueryBuilder\PGSQL
 */
final class QueryBuilder extends ParentQueryBuilder implements BuilderInterface
{
    /**
     * @param Table $table
     * @param array<mixed> $values
     *
     * @return int
     * @throws Exception
     */
    public function insert(Table $table, array $values): int
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

    /**
     * @param Table $table
     * @param FieldCollection $values
     * @param string[] $updatable
     *
     * @return Insert
     */
    public function insertUpdate(Table $table, FieldCollection $values, array $updatable): Insert
    {
        return new Insert($table->getName(), $values->getCollectionItems(), $updatable);
    }
}
