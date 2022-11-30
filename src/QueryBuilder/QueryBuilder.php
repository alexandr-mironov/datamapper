<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

use DataMapper\Entity\Column as ColumnEntity;
use DataMapper\Entity\ColumnCollection;
use DataMapper\Entity\ConditionCollection;
use DataMapper\Entity\FieldCollection;
use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Definitions\Column;
use DataMapper\QueryBuilder\Exceptions\{Exception, UnsupportedException};
use DataMapper\QueryBuilder\Statements\{CreateTable,
    Delete,
    DropTable,
    Insert,
    Select,
    SelectWrapper,
    StatementInterface,
    With};
use PDO;
use PDOStatement;

/**
 * Class QueryBuilder
 *
 * @package DataMapper\QueryBuilder
 */
class QueryBuilder implements BuilderInterface
{
    private const PGSQL = 'pgsql';

    private const MYSQL = 'mysql';

    public const SQL1999 = 'SQL:1999';

    public const POSTGRESQL = 'postgresql';

    private const DBMS = [
        self::PGSQL => PGSQL\QueryBuilder::class,
        self::MYSQL => MySQL\QueryBuilder::class,
    ];

    /** @var BuilderInterface */
    public BuilderInterface $adapter;

    /** @var StatementInterface */
    private StatementInterface $statement;

    /**
     * QueryBuilder constructor.
     *
     * @param PDO $pdo todo remove PDO dependency from constructor - Query builders only build queries, they didn't execute any queries or something
     * @param bool $beautify
     *
     */
    public function __construct(
        protected PDO $pdo,
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
     * @param array{key: string, value: mixed, type: string} $values ['key' => ..., 'value' => ..., 'type' => ...]
     *
     * @return Insert
     *
     */
    public function insert(Table $table, array $values): Insert
    {
        return new Insert($table, $values);
    }

    /**
     * @param Table $table
     * @param FieldCollection $values
     * @param string[] $updatable
     *
     * @return bool
     * @throws Exception
     */
    public function insertUpdate(Table $table, FieldCollection $values, array $updatable): bool
    {
        $query = (string)$this->adapter->insert($table, $values, $updatable);
        $statement = $this->pdo->prepare($query);

        if (!$statement) {
            throw new Exception('Invalid query ' . $query);
        }

        return $statement->execute($values->getCollectionItems());
    }

    /**
     * @param Table $name
     * @param ColumnCollection $columns
     * @param array<mixed> $options
     *
     * @return bool
     * @throws Exception
     */
    public function createTable(Table $name, ColumnCollection $columns, array $options = []): bool
    {
        $createTableStatement = new CreateTable($name, $options);
        /** @var ColumnEntity $column */
        foreach ($columns as $column) {
            $columnDefinition = new Column(
                $column->getKey(),
                $column->getType(),
                $column->getOptions()
            );

            $createTableStatement->addColumn($columnDefinition);
        }

        $pdoStatement = $this->pdo->query((string)$createTableStatement);

        if (!$pdoStatement) {
            throw new Exception('Invalid query ' . (string)$createTableStatement);
        }

        return $pdoStatement->execute();
    }

    /**
     * @param Table $table
     * @param ConditionCollection $conditions
     *
     * @return bool
     * @throws Exception
     */
    public function delete(Table $table, ConditionCollection $conditions): bool
    {
        $statement = new Delete($table);
        /** @var ConditionInterface $condition */
        foreach ($conditions as $condition) {
            $statement->addWhereCondition($condition);
        }
        $query = $statement->__toString();

        $pdoStatement = $this->pdo->query($query);

        if (!$pdoStatement) {
            throw new Exception('Invalid query ' . $query);
        }

        return $pdoStatement->execute();
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

    public function dropTable(Table $table, array $options): bool
    {
        $dropTableStatement = new DropTable($table->getName());
        $pdoStatement = $this->pdo->query((string)$dropTableStatement);

        if (!$pdoStatement) {
            throw new Exception('Invalid query ' . (string)$dropTableStatement);
        }

        return $pdoStatement->execute();
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

    public function alterTable(): StatementInterface
    {
        // TODO: Implement alterTable() method.
    }
}
