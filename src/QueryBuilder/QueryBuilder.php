<?php


namespace DataMapper\QueryBuilder;


use DataMapper\Entity\ColumnCollection;
use DataMapper\Entity\ConditionCollection;
use DataMapper\Entity\FieldCollection;
use DataMapper\Entity\Table;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Definitions\Column;
use DataMapper\QueryBuilder\Exceptions\{Exception, UnsupportedException};
use DataMapper\QueryBuilder\Statements\{CreateTable, Delete, Select, SelectWrapper, StatementInterface, With};
use PDO;
use PDOStatement;

/**
 * Class QueryBuilder
 * @package DataMapper\QueryBuilder
 */
class QueryBuilder implements BuilderInterface
{
    private const PGSQL = 'pgsql';

    private const MYSQL = 'mysql';

    /** @var StatementInterface */
    private StatementInterface $statement;

    /** @var array|null[] */
    private array $dbInfo = [null, null];

    private const DBMS = [
        self::PGSQL => PGSQL\QueryBuilder::class,
        self::MYSQL => MySQL\QueryBuilder::class
    ];

    /** @var BuilderInterface */
    private BuilderInterface $adapter;

    /**
     * QueryBuilder constructor.
     * @param PDO $pdo
     * @throws UnsupportedException
     */
    public function __construct(private PDO $pdo)
    {
        $this->adapter = $this->detectDBMS();
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
     * @return PDOStatement
     * @throws Exception
     */
    public function execute(string $query): PDOStatement
    {
        $result = $this->pdo->query($query);
        if (!$result) {
            throw new Exception('Invalid query');
        }
        return $result;
    }

    /**
     * @param string $table
     * @param array $values ['key' => ..., 'value' => ..., 'type' => ...]
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
     * @param array $updatable
     * @return bool
     * @throws Exception
     */
    public function insertUpdate(Table $table, FieldCollection $values, array $updatable): bool
    {
        return $this->adapter->insertUpdate($table, $values, $updatable);
//        $statement = $this->pdo->query((string)new Insert($table, $values, $updatable));
//        foreach ($values as $value) {
//            $statement->bindParam($value['key'], $value['value'], $this->getType($value['type']));
//        }
//        return $statement->execute();
    }

    /**
     * @return false|PDOStatement
     */
    private function lastInsertId(): false|PDOStatement
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param Table $name
     * @param ColumnCollection $columns
     * @param array $options
     *
     * @return bool
     */
    public function createTable(Table $name, ColumnCollection $columns, array $options = []): bool
    {
        $createTableStatement = new CreateTable($name, $options);
        foreach ($columns as $column) {
            $columnDefinition = new Column(
                $column->getKey(),
                $column->getType(),
                $column->getOptions()
            );

            $createTableStatement->addColumn($columnDefinition);
        }

        return $this->pdo->query((string)$createTableStatement)->execute();
    }

    /**
     * @param mixed $type
     * @return int
     */
    protected function getType(mixed $type): int
    {
        return match ($type) {
            'integer', 'float' => PDO::PARAM_INT,
            'string', 'datetime' => PDO::PARAM_STR,
            'boolean' => PDO::PARAM_BOOL,
        };
    }

    /**
     * @return array
     */
    private function getDBInfo(): array
    {
        return $this->dbInfo;
    }

    /**
     * @param Table $table
     * @param ConditionCollection $conditions
     * @return bool
     */
    public function delete(Table $table, ConditionCollection $conditions): bool
    {
        $statement = new Delete($table);
        /** @var ConditionInterface $condition */
        foreach ($conditions as $condition) {
            $statement->addWhereCondition($condition);
        }
        $query = $statement->__toString();
        return $this->pdo->query($query)->execute();
    }

    /**
     * @return BuilderInterface
     * @throws UnsupportedException
     */
    private function detectDBMS(): BuilderInterface
    {
        // todo replace to extracting dbms type from schema?
        $dbms = 'pgsql';
        if (array_key_exists($dbms, self::DBMS)) {
            $className = self::DBMS[$dbms];
            return new $className($this->pdo);
        }
        throw new UnsupportedException('Unsupported DBMS');
    }

    /**
     * @param Select[] $selects
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
}