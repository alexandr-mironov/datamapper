<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\MySQL;

use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\MySQL\Statements\Insert;
use DataMapper\QueryBuilder\QueryBuilder as ParentQueryBuilder;

/**
 * Class QueryBuilder
 *
 * @package DataMapper\QueryBuilder\MySQL
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
