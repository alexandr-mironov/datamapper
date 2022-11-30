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

    /**
     * @param Table $table
     * @param ConditionInterface ...$conditions
     *
     * @return Select
     */
    public function select(Table $table, ConditionInterface ...$conditions): Select;

    /**
     * @param Table $table
     * @param ConditionCollection $conditions
     *
     * @return Delete
     */
    public function delete(Table $table, ConditionCollection $conditions): Delete;

    /**
     * @param Table $table
     * @param ColumnCollection $columns
     * @param array $options
     *
     * @return CreateTable
     */
    public function createTable(Table $table, ColumnCollection $columns, array $options = []): CreateTable;

    /**
     * @param Table $table
     * @param array $options
     *
     * @return DropTable
     */
    public function dropTable(Table $table, array $options = []): DropTable;

    /**
     * @param Table $table
     *
     * @return AlterTable
     */
    public function alterTable(Table $table): AlterTable;
}
