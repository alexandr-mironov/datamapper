<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

use DataMapper\Entity\Column as ColumnEntity;
use DataMapper\Entity\ColumnCollection;
use DataMapper\Entity\FieldCollection;
use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Definitions\Column;
use DataMapper\QueryBuilder\Exceptions\{Exception};
use DataMapper\QueryBuilder\Statements\{AlterTable,
    CreateTable,
    Delete,
    DropTable,
    Insert,
    Select,
    SelectWrapper,
    StatementInterface,
    With
};
use PDO;

/**
 * Class QueryBuilder
 *
 * @package DataMapper\QueryBuilder
 */
class QueryBuilder implements BuilderInterface
{
    public const MYSQL = 'mysql';

    public const SQL1999 = 'SQL:1999';

    public const POSTGRESQL = 'postgresql';

    /** @var BuilderInterface */
    public BuilderInterface $adapter;

    /** @var StatementInterface */
    private StatementInterface $statement;

    /**
     * QueryBuilder constructor.
     *
     * @param bool $beautify
     *
     */
    public function __construct(
        public bool $beautify = false
    ) {

    }

    /**
     * @param Table $table
     * @param ConditionInterface ...$conditions
     *
     * @return Select
     */
    public function select(Table $table, ConditionInterface ...$conditions): Select
    {
        return new Select($table, ...$conditions);
    }

    /**
     * @param Table $table
     * @param FieldCollection $values
     * @param string[] $updatable
     *
     * @return Insert
     */
    public function insert(Table $table, FieldCollection $values, array $updatable = []): Insert
    {
        return new Insert($table->getName(), $values, $updatable);
    }

    /**
     * @param Table $table
     * @param ColumnCollection $columns
     * @param array<mixed> $options
     *
     * @return CreateTable
     * @throws \Exception
     */
    public function createTable(Table $table, ColumnCollection $columns, array $options = []): CreateTable
    {
        $createTableStatement = new CreateTable($table, $options);
        /** @var ColumnEntity $column */
        foreach ($columns as $column) {
            $columnDefinition = new Column(
                $column->getKey(),
                $column->getType(),
                $column->getOptions()
            );

            $createTableStatement->addColumn($columnDefinition);
        }

        return $createTableStatement;
    }

    /**
     * @param Table $table
     * @param ConditionInterface ...$conditions
     *
     * @return Delete
     */
    public function delete(Table $table, ConditionInterface ...$conditions): Delete
    {
        return new Delete($table, ...$conditions);
    }

    /**
     * @param Select[] $selects
     *
     * @return $this
     * @throws Exception
     */
    public function with(array $selects): static
    {
        $wrappers = [];
        foreach ($selects as $key => $select) {
            if (!$select instanceof Select) {
                throw new Exception('Invalid select statement provided');
            }
            $alias = null;
            if (is_string($key)) {
                $alias = $key;
            }
            $wrappers[] = new SelectWrapper($select, $alias);
        }
        $this->statement = new With(...$wrappers);

        return $this;
    }

    /**
     * @param Table $table
     * @param array<mixed> $options
     *
     * @return DropTable
     */
    public function dropTable(Table $table, array $options = []): DropTable
    {
        $dropTableStatement = new DropTable($table);

        if (
            array_key_exists('cascade', $options)
            && $options['cascade']
        ) {
            $dropTableStatement->cascade = true;
        }

        if (
            array_key_exists('temporary', $options)
            && $options['temporary']
        ) {
            $dropTableStatement->temporary = true;
        }

        if (
            array_key_exists('ifExists', $options)
            && $options['ifExists']
        ) {
            $dropTableStatement->ifExists = true;
        }

        if (
            array_key_exists('restrict', $options)
            && $options['restrict']
        ) {
            $dropTableStatement->restrict = true;
        }

        return $dropTableStatement;
    }

    /**
     * @param Table $table
     *
     * @return AlterTable
     */
    public function alterTable(Table $table): AlterTable
    {
        return new AlterTable($table);
    }

    /**
     * @param mixed $type
     *
     * @return int
     */
    protected function getType(mixed $type): int
    {
        return match ($type) {
            'integer', 'float' => PDO::PARAM_INT,
            'boolean' => PDO::PARAM_BOOL,
            'string', 'datetime' => PDO::PARAM_STR,
            default => PDO::PARAM_STR,
        };
    }
}
