<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\PGSQL;

use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\PGSQL\Statements\Insert;
use DataMapper\QueryBuilder\QueryBuilder as ParentQueryBuilder;

/**
 * Class QueryBuilder
 *
 * @package DataMapper\QueryBuilder\PGSQL
 */
final class QueryBuilder extends ParentQueryBuilder implements BuilderInterface
{
    /**
     * @param Table $table
     * @param string[] $keys
     * @param string[] $updatable
     *
     * @return Insert
     */
    public function insert(Table $table, array $keys, array $updatable = []): Insert
    {
        return new Insert($table->getName(), $keys, $updatable);
    }
}
