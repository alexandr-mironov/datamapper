<?php

declare(strict_types=1);

namespace DataMapper;

use DataMapper\Attributes\{Column, Table};
use DataMapper\Entity\ConditionCollection;
use DataMapper\Entity\Field;
use DataMapper\Entity\FieldCollection;
use DataMapper\Helpers\ColumnHelper;
use DataMapper\QueryBuilder\BuilderInterface;
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\Exceptions\{Exception, Exception as QueryBuilderException, UnsupportedException};
use DataMapper\QueryBuilder\PGSQL\QueryBuilder as PostgreSQLQueryBuilder;
use DataMapper\QueryBuilder\QueryBuilder;
use DataMapper\QueryBuilder\Statements\Select;
use PDO;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

/**
 * Class DataMapper
 *
 * @package DataMapper
 */
class DataMapper
{
    private BuilderInterface $queryBuilder;

    /**
     * DataMapper constructor.
     *
     * @param PDO $pdo
     * @param bool $beautify
     */
    public function __construct(
        private PDO $pdo,
        public bool $beautify = false
    ) {

    }

    public static function init(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        ?array $options = null,
        bool $beautify = false
    ): self {
        $dataMapper = new self(
            new PDO($dsn, $username, $password, $options),
            $beautify
        );

        $dataMapper->detectDBMS($dsn);

        return $dataMapper;
    }

    /**
     * @param string $dsn
     *
     * @throws UnsupportedException
     */
    private function detectDBMS(string $dsn)
    {
        $scheme = parse_url($dsn, PHP_URL_SCHEME);

        $this->queryBuilder = match ($scheme) {
            QueryBuilder::POSTGRESQL => new PostgreSQLQueryBuilder($this->pdo),
            QueryBuilder::SQL1999 => new QueryBuilder($this->pdo, $this->beautify),
            default => throw new UnsupportedException('Unsupported DBMS')
        };
    }

    /**
     * @param string $className
     *
     * @return Select
     *
     * @throws QueryBuilderException
     * @throws UnsupportedException|ReflectionException
     */
    public function find(string $className): Select
    {
        if (!class_exists($className)) {
            throw new Exception('Invalid class provided ' . $className);
        }

        return $this->getQueryBuilder()
            ->find(
                $this->getTable(new ReflectionClass($className)),
                $className
            );
    }

    /**
     * @return BuilderInterface
     */
    private function getQueryBuilder(): BuilderInterface
    {
        return $this->queryBuilder;
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return Entity\Table
     * @throws QueryBuilderException
     */
    private function getTable(ReflectionClass $reflection): Entity\Table
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
     * @throws UnsupportedException
     * @throws Exceptions\Exception
     */
    public function store(object $model): bool
    {
        $reflection = new ReflectionObject($model);
        $fields = $this->getFields($reflection, $model);
        $fieldsForUpdate = $fields->getKeys();

        if (ColumnHelper::hasPrimaryKey($reflection)) {
            $key = ColumnHelper::getPrimaryKeyColumnName($reflection);
            $index = array_search($key, $fieldsForUpdate);
            unset($fieldsForUpdate[$index]);
        }

        return $this->getQueryBuilder()
            ->insertUpdate(
                $this->getTable($reflection),
                $fields,
                $fieldsForUpdate
            );
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

    public function add(object $model): object
    {
        $reflection = new ReflectionObject($model);
        $fields = $this->getFields($reflection, $model);

        $insertStatement = $this->getQueryBuilder()
            ->insert(
                $this->getTable($reflection),
                $fields,
                $fieldsForUpdate
            );

        return $model;
    }

    /**
     * @param object $model
     *
     * @return bool
     *
     * @throws Exception
     * @throws Exceptions\Exception
     */
    public function delete(object $model): bool
    {
        $reflection = new ReflectionObject($model);

        return $this->getQueryBuilder()
            ->delete(
                $this->getTable($reflection),
                $this->getConditionsByModel($reflection, $model)
            );
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     *
     * @return ConditionCollection
     * @throws Exceptions\Exception|QueryBuilderException
     */
    private function getConditionsByModel(ReflectionObject $reflection, object $model): ConditionCollection
    {
        return match (true) {
            ColumnHelper::hasPrimaryKey($reflection) => new ConditionCollection(
                [$this->getPrimaryKeyValue($reflection, $model)]
            ),
            ColumnHelper::hasUnique($reflection) => new ConditionCollection(
                [$this->getUniqueValue($reflection, $model)]
            ),
            default => new ConditionCollection($this->buildConditionArray($reflection, $model))
        };
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     *
     * @return Equal
     * @throws QueryBuilderException
     * @throws Exceptions\Exception
     */
    private function getPrimaryKeyValue(ReflectionObject $reflection, object $model): Equal
    {
        $key = ColumnHelper::getPrimaryKeyColumnName($reflection);

        return new Equal([$key, $model->$key]);
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     *
     * @return Equal
     * @throws QueryBuilderException
     * @throws Exceptions\Exception
     */
    private function getUniqueValue(ReflectionObject $reflection, object $model): Equal
    {
        $key = ColumnHelper::getFirstUniqueColumnName($reflection);

        return new Equal([$key, $model->$key]);
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     *
     * @return ConditionInterface[]
     * @throws QueryBuilderException
     */
    private function buildConditionArray(ReflectionObject $reflection, object $model): array
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
     * @param object|class-string $class
     * @param array<mixed> $options
     *
     * @return bool
     *
     * @throws QueryBuilderException
     * @throws ReflectionException
     * @throws UnsupportedException
     */
    public function createTable(object|string $class, array $options = []): bool
    {
        $reflection = new ReflectionClass((is_object($class)) ? $class::class : $class);

        return $this->getQueryBuilder()
            ->createTable(
                $this->getTable($reflection),
                ColumnHelper::getColumns($reflection),
                $options
            );
    }

    /**
     * @param object|string $class
     * @param array $options
     *
     * @return bool
     * @throws QueryBuilderException
     * @throws ReflectionException
     * @throws UnsupportedException
     */
    public function dropTable(object|string $class, array $options = []): bool
    {
        $reflection = new ReflectionClass((is_object($class)) ? $class::class : $class);

        return $this->getQueryBuilder()
            ->dropTable(
                $this->getTable($reflection),
                $options
            );
    }
}
