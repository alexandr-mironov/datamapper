<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Attributes\{Column, Table};
use DataMapper\Entity\Field;
use DataMapper\Entity\FieldCollection;
use DataMapper\Helpers\ColumnHelper;
use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\MySQL\QueryBuilder as MySQLQueryBuilder;
use DataMapper\Exceptions\Exception;
use DataMapper\QueryBuilder\Exceptions\{
    Exception as QueryBuilderException,
    UnsupportedException};
use DataMapper\QueryBuilder\PGSQL\QueryBuilder as PostgreSQLQueryBuilder;
use DataMapper\QueryBuilder\QueryBuilder;
use DataMapper\QueryBuilder\Statements\Select;
use DataMapper\QueryBuilder\Statements\StatementInterface;
use DataMapper\QueryBuilder\Statements\WhereTrait;
use Generator;
use PDO;
use PDOStatement;
use ReflectionClass;
use ReflectionException;

/**
 * Class DataMapper
 *
 * @package DataMapper
 */
class DataMapper
{
    use WhereTrait;

    private BuilderInterface $queryBuilder;

    private ?StatementInterface $statement = null;

    /** @var class-string $entityClass used as return type of select statements */
    private string $entityClass;

    /**
     * DataMapper constructor.
     *
     * @param PDO $pdo
     * @param string $dbmsAlias
     * @param bool $beautify
     *
     * @throws QueryBuilderException
     * @throws UnsupportedException
     */
    public function __construct(
        private PDO $pdo,
        string $dbmsAlias = QueryBuilder::SQL1999,
        public bool $beautify = false
    ) {
        $dbms = $this->getBuilderClassNameByAlias($dbmsAlias);

        if (!class_exists($dbms)) {
            throw new QueryBuilderException('Invalid DBMS class provided ' . $dbms);
        }

        $dbmsInstance = new $dbms($this->beautify);

        if (!$dbmsInstance instanceof BuilderInterface) {
            throw new QueryBuilderException('Invalid DBMS class provided ' . $dbms);
        }

        $this->queryBuilder = $dbmsInstance;
    }

    /**
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array|null $options
     * @param bool $beautify
     *
     * @return static
     */
    public static function init(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        ?array $options = null,
        bool $beautify = false
    ): self
    {
        $pdo = new PDO($dsn, $username, $password, $options);

        return self::construct($pdo, (string)parse_url($dsn, PHP_URL_SCHEME), $beautify);
    }

    /**
     * @param PDO $pdo
     * @param string $dbmsAlias
     * @param bool $beautify
     *
     * @return static
     * @throws QueryBuilderException
     * @throws UnsupportedException
     */
    public static function construct(
        PDO $pdo,
        string $dbmsAlias = QueryBuilder::SQL1999,
        bool $beautify = false
    ): self {
        return new self($pdo, $dbmsAlias, $beautify);
    }

    /**
     * @param string $alias
     *
     * @return class-string
     * @throws UnsupportedException
     */
    private static function getBuilderClassNameByAlias(string $alias): string
    {
        return match ($alias) {
            // todo: other adapters
            QueryBuilder::POSTGRESQL => PostgreSQLQueryBuilder::class,
            QueryBuilder::SQL1999 => QueryBuilder::class,
            QueryBuilder::MYSQL => MySQLQueryBuilder::class,
            default => throw new UnsupportedException('Unsupported DBMS')
        };
    }

    /**
     * @param string $className
     * @param ConditionInterface ...$conditions
     *
     * @return DataMapper
     *
     * @throws QueryBuilderException
     * @throws ReflectionException
     */
    public function find(string $className, ConditionInterface ...$conditions): self
    {
        if (!class_exists($className)) {
            throw new Exception('Invalid class provided ' . $className);
        }

        $this->entityClass = $className;

        $this->statement = $this->queryBuilder->select($this->getTable(new ReflectionClass($className)), ...$conditions);

        return $this;
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return Entity\Table
     * @throws Exception
     */
    public function getTable(ReflectionClass $reflection): Entity\Table
    {
        $classAttributes = $reflection->getAttributes(Table::class);
        if (count($classAttributes)) {
            foreach ($classAttributes as $attribute) {
                /** @var Table $table */
                $table = $attribute->newInstance();

                return $table->getName();
            }
        }
        throw new Exception("model doesn't have required attribute Table");
    }

    /**
     * @param object $model
     *
     * @return bool
     *
     * @throws QueryBuilderException
     * @throws Exceptions\Exception
     */
    public function store(object $model): bool
    {
        $reflection = new ReflectionClass($model);
        $fields = $this->getFields($reflection, $model);
        $fieldsForUpdate = $fields->getKeys();

        if (ColumnHelper::hasPrimaryKey($reflection)) {
            $key = ColumnHelper::getPrimaryKeyColumnName($reflection);
            $index = array_search($key, $fieldsForUpdate);
            unset($fieldsForUpdate[$index]);
        }

        $insertStatement = $this->getQueryBuilder()
            ->insert(
                $this->getTable($reflection),
                $fields,
                $fieldsForUpdate
            );

        return (bool)$this->execute((string)$insertStatement);
    }

    /**
     * @param ReflectionClass $reflection
     * @param object $model
     *
     * @return FieldCollection
     */
    private function getFields(ReflectionClass $reflection, object $model): FieldCollection
    {
        $collection = new FieldCollection();
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $columnAttributes = $property->getAttributes(Column::class);
            if (count($columnAttributes)) {
                foreach ($columnAttributes as $attribute) {
                    /** @var Column $column */
                    $column = $attribute->newInstance();
                    $columnType = $column->getType();
                    $collection->push(
                        new Field(
                            $column->getName(),
                            $column->castToType($property->getValue($model), $columnType),
                            $columnType
                        )
                    );

                }
            }
        }

        return $collection;
    }

    /**
     * @return BuilderInterface
     */
    private function getQueryBuilder(): BuilderInterface
    {
        return $this->queryBuilder;
    }

    /**
     * @param object $model
     *
     * @return object
     * @throws Exception
     */
    public function add(object $model): object
    {
        $reflection = new ReflectionClass($model);
        $fields = $this->getFields($reflection, $model);

        $insertStatement = $this->getQueryBuilder()
            ->insert(
                $this->getTable($reflection),
                $fields
            );

        // todo: add logic here

        return $model;
    }

    /**
     * @param object|class-string $model
     * @param ConditionInterface ...$conditions
     *
     * @return bool
     *
     * @throws QueryBuilderException
     * @throws ReflectionException
     * @throws Exception
     */
    public function delete(object|string $model, ConditionInterface ...$conditions): bool
    {
        $reflection = new ReflectionClass((is_object($model)) ? $model::class : $model);

        $conditions = is_object($model) ? $this->getConditionsByModel($reflection, $model) : $conditions;

        $deleteStatement = $this->getQueryBuilder()
            ->delete(
                $this->getTable($reflection),
                ...$conditions
            );

        return (bool)$this->execute((string)$deleteStatement);
    }

    /**
     * @param ReflectionClass $reflection
     * @param object $model
     *
     * @return ConditionInterface[]
     * @throws Exception
     * @throws QueryBuilderException
     */
    private function getConditionsByModel(ReflectionClass $reflection, object $model): array
    {
        return match (true) {
            ColumnHelper::hasPrimaryKey($reflection) => [$this->getPrimaryKeyValue($reflection, $model)],
            ColumnHelper::hasUnique($reflection) => [$this->getUniqueValue($reflection, $model)],
            default => $this->buildConditionArray($reflection, $model)
        };
    }

    /**
     * @param ReflectionClass $reflection
     * @param object $model
     *
     * @return Equal
     * @throws QueryBuilderException
     * @throws Exceptions\Exception
     */
    private function getPrimaryKeyValue(ReflectionClass $reflection, object $model): Equal
    {
        $key = ColumnHelper::getPrimaryKeyColumnName($reflection);

        return new Equal([$key, $model->$key]);
    }

    /**
     * @param ReflectionClass $reflection
     * @param object $model
     *
     * @return Equal
     * @throws QueryBuilderException
     * @throws Exceptions\Exception
     */
    private function getUniqueValue(ReflectionClass $reflection, object $model): Equal
    {
        $key = ColumnHelper::getFirstUniqueColumnName($reflection);

        return new Equal([$key, $model->$key]);
    }

    /**
     * @param ReflectionClass $reflection
     * @param object $model
     *
     * @return ConditionInterface[]
     * @throws QueryBuilderException
     */
    private function buildConditionArray(ReflectionClass $reflection, object $model): array
    {
        $result = [];
        foreach (ColumnHelper::getColumnIterator($reflection) as $column) {
            $key = $column->getName();
            $result[] = new Equal(
                [
                    $key,
                    $model->$key,
                ]
            );
        }

        return $result;
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
     * @param object|class-string $class
     * @param array<mixed> $options
     *
     * @return bool
     *
     * @throws QueryBuilderException
     * @throws ReflectionException
     * @throws Exception
     */
    public function createTable(object|string $class, array $options = []): bool
    {
        $reflection = new ReflectionClass((is_object($class)) ? $class::class : $class);

        $createTableStatement = $this->getQueryBuilder()
            ->createTable(
                $this->getTable($reflection),
                ColumnHelper::getColumns($reflection),
                $options
            );

        return (bool)$this->execute((string)$createTableStatement);
    }

    /**
     * @param object|class-string $class
     * @param array<mixed> $options
     *
     * @return bool
     * @throws QueryBuilderException
     * @throws ReflectionException
     * @throws Exception
     */
    public function dropTable(object|string $class, array $options = []): bool
    {
        $reflection = new ReflectionClass((is_object($class)) ? $class::class : $class);

        $dropTableStatement = $this->queryBuilder
            ->dropTable(
                $this->getTable($reflection),
                $options
            );

        return (bool)$this->execute((string)$dropTableStatement);
    }

    /**
     * @return object
     * @throws Exception
     */
    public function getOne(): object
    {
        if (!$this->statement instanceof Select) {
            throw new Exception('This method only available for Select statements');
        }

        $this->statement->limit = 1;
        $this->statement->offset = $this->offset;
        $this->statement->wheres = $this->wheres;
        $this->statement->order = $this->order;

        $result = $this->execute((string)$this->statement);
        $className = $this->entityClass;

        return new $className(...$result);
    }

    /**
     * @return object[]
     * @throws Exception
     */
    public function getArray(): array
    {
        $collection = [];
        foreach ($this->getIterator() as $item) {
            $collection[] = $item;
        }

        return $collection;
    }

    /**
     * @return Generator<object>
     * @throws Exception
     */
    public function getIterator(): Generator
    {
        if (!$this->statement instanceof Select) {
            throw new Exception('This method only available for Select statements');
        }

        $this->statement->limit = $this->limit;
        $this->statement->offset = $this->offset;
        $this->statement->wheres = $this->wheres;
        $this->statement->order = $this->order;

        $result = $this->execute((string)$this->statement);
        $className = $this->entityClass;
        foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
            yield new $className(...$row);
        }
    }

    /**
     * @param string $key
     *
     * @return array<string, object>
     * @throws Exception
     */
    public function getMap(string $key): array
    {
        $collection = [];
        foreach ($this->getIterator() as $item) {
            if (!property_exists($item, $key)) {
                throw new Exception('`' . $key . '` is not in field list');
            }
            $collection[(string)$item->$key] = $item;
        }

        return $collection;
    }

    public function getSQL(): string
    {
        if ($this->statement instanceof Select) {
            $this->statement->limit = $this->limit;
            $this->statement->offset = $this->offset;
            $this->statement->wheres = $this->wheres;
            $this->statement->order = $this->order;
        }
        
        return (string)$this->statement;
    }
}
