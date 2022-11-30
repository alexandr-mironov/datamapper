<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

use DataMapper\Entity\ColumnCollection;
use DataMapper\Entity\ConditionCollection;
use DataMapper\Entity\FieldCollection;
use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Statements\AlterTable;
use DataMapper\QueryBuilder\Statements\CreateTable;
use DataMapper\QueryBuilder\Statements\Delete;
use DataMapper\QueryBuilder\Statements\DropTable;
use DataMapper\QueryBuilder\Statements\Insert;
use DataMapper\QueryBuilder\Statements\Select;

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
     * @return Insert
     */
    public function insert(Table $table, FieldCollection $values, array $updatable = []): Insert;

    public function select(Table $table, ConditionInterface ...$conditions): Select;

    public function delete(Table $table, ConditionCollection $conditions): Delete;

    public function createTable(Table $name, ColumnCollection $columns, array $options = []): CreateTable;

    public function dropTable(Table $name, array $options = []): DropTable;

    public function alterTable(): AlterTable;
}
