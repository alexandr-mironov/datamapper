<?php


namespace DataMapper\QueryBuilder;


use PDO;
use PDOStatement;
use DataMapper\QueryBuilder\Conditions\WhereCondition;
use DataMapper\QueryBuilder\Definitions\Column;
use DataMapper\QueryBuilder\Exceptions\{Exception, UnsupportedException};
use DataMapper\QueryBuilder\Statements\{AbstractStatementWithWhere,
    CreateTable,
    Delete,
    Insert,
    Select,
    SelectWrapper,
    StatementInterface,
    With
};

/**
 * Class QueryBuilder
 * @package DataMapper\QueryBuilder
 */
class QueryBuilder implements BuilderInterface
{
    /** @var StatementInterface */
    private StatementInterface $statement;

    /** @var array|null[] */
    private array $dbInfo = [null, null];

    private const DBMS = [
        'pgsql' => PGSQL\QueryBuilder::class,
        'mysql' => MySQL\QueryBuilder::class
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
     * @param string $table
     *
     * @return $this
     */
    public function find(string $table): static
    {
        $this->statement = new Select($table);
        return $this;
    }

    /**
     * @param array $condition
     *
     * @return $this
     */
    public function where(array $condition): static
    {
        $whereCondition = new WhereCondition($condition);
        if ($this->statement instanceof AbstractStatementWithWhere) {
            $this->statement->addWhereCondition($whereCondition);
        }
        return $this;
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
     * @param string $table
     * @param array $values
     * @param array $updatable
     * @return bool
     */
    public function insertUpdate(string $table, array $values, array $updatable): bool
    {
        $statement = $this->pdo->query((string)new Insert($table, $values, $updatable));
        foreach ($values as $value) {
            $statement->bindParam($value['key'], $value['value'], $this->getType($value['type']));
        }
        return $statement->execute();
    }

    /**
     * @return false|PDOStatement
     */
    private function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @param string $name
     * @param array $columns
     * @param array $options
     *
     * @return bool
     */
    public function createTable(string $name, array $columns, array $options = []): bool
    {
        $createTableStatement = new CreateTable($name);
        foreach ($columns as $column) {
            $columnDefinition = new Column($column['key'], $column['type']);

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
     * @param string $table
     * @param array $conditions
     * @return bool
     */
    public function delete(string $table, array $conditions)
    {
        $statement = new Delete($table);
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