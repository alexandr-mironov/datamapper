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
class QueryBuilder implements BuilderWrapperInterface
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
     * @param PDO $pdo
     * @param bool $beautify
     *
     * @throws UnsupportedException
     */
    public function __construct(private PDO $pdo, public bool $beautify = false)
    {
        $this->adapter = $this->getAdapter();
    }

    /**
     * @return BuilderInterface
     * @throws UnsupportedException
     */
    private function getAdapter(): BuilderInterface
    {
        $dbms = $this->detectDBMS();
        if (array_key_exists($dbms, self::DBMS)) {
            $className = self::DBMS[$dbms];

            return new $className($this->pdo);
        }
        throw new UnsupportedException('Unsupported DBMS');
    }

    /**
     * @return string
     */
    private function detectDBMS(): string
    {
        // todo replace to extracting dbms type from schema?
        return 'pgsql';

    }

    /**
     * @param Table $table
     * @param string $className
     *
     * @return Select
     */
    public function find(Table $table, string $className): Select
    {
        return new Select($this, $table, $className);
    }

    /**
     * @param string $query
     *
     * @return PDOStatement
     * @throws Exception
     */
    public function execute(string $query): PDOStatement
    {
        $result = $this->pdo->query($query);
        if (!$result) {
            throw new Exception('Invalid query ' . $query);
        }

        return $result;
    }

    /**
     * @param string $table
     * @param array{key: string, value: mixed, type: string} $values ['key' => ..., 'value' => ..., 'type' => ...]
     *
     * @return int
     *
     * @throws Exception
     */
    public function insert(string $table, array $values): int
    {
        return $this->adapter->insert($table, $values);
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
        $query = (string)$this->adapter->getInsert($table, $values, $updatable);
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
}
