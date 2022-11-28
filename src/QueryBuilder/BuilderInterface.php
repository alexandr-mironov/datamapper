<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

use DataMapper\Entity\ColumnCollection;
use DataMapper\Entity\ConditionCollection;
use DataMapper\Entity\FieldCollection;
use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Statements\StatementInterface;

/**
 * Interface BuilderInterface
 *
 * @package DataMapper\QueryBuilder
 */
interface BuilderInterface
{
    /**
     * @param Table $table
     * @param FieldCollection $values
     * @param string[] $updatable
     *
     * @return StatementInterface
     */
    public function insert(Table $table, FieldCollection $values, array $updatable = []): StatementInterface;

    public function select(Table $table, string $className): StatementInterface;

    public function delete(Table $table, ConditionCollection $conditions): StatementInterface;

    public function createTable(Table $name, ColumnCollection $columns, array $options = []): StatementInterface;

    public function dropTable(Table $name, array $options = []): StatementInterface;

    public function alterTable(): StatementInterface;
}
