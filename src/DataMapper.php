<?php

declare(strict_types=1);

namespace DataMapper;


use DataMapper\Attributes\{Column, Table};
use DataMapper\Entity\ColumnCollection;
use DataMapper\Entity\ConditionCollection;
use DataMapper\Entity\Field;
use DataMapper\Entity\FieldCollection;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\Exceptions\{Exception, Exception as QueryBuilderException, UnsupportedException};
use DataMapper\QueryBuilder\QueryBuilder;
use DataMapper\QueryBuilder\Statements\Select;
use Generator;
use PDO;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

/**
 * Class DataMapper
 * @package DataMapper
 */
class DataMapper
{
    /**
     * DataMapper constructor.
     * @param PDO $pdo
     */
    public function __construct(
        private PDO $pdo,
    )
    {

    }

    /**
     * @param string $className
     *
     * @return Select
     *
     * @throws QueryBuilderException
     * @throws UnsupportedException
     */
    public function find(string $className): Select
    {
        if (!class_exists($className)) {
            throw new Exception('Invalid class provided ' . $className);
        }
        return $this->getQueryBuilder()->find(
            $this->getTable(new ReflectionClass($className)),
            $className
        );
    }

    /**
     * @param object $model
     *
     * @return bool
     *
     * @throws QueryBuilderException
     * @throws UnsupportedException
     */
    public function store(object $model): bool
    {
        $reflection = new ReflectionObject($model);
        $fields = $this->getFields($reflection, $model);
        $fieldsForUpdate = array_column($fields, 'key');

        if ($this->hasPrimaryKey($reflection)) {
            $key = $this->getPrimaryKeyColumnName($reflection);
            $index = array_search($key, $fieldsForUpdate);
            unset($fieldsForUpdate[$index]);
        }

        return $this->getQueryBuilder()->insertUpdate(
            $this->getTable($reflection),
            $fields,
            $fieldsForUpdate
        );
    }

    /**
     * @param object $model
     *
     * @return bool
     *
     * @throws Exception
     * @throws UnsupportedException
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
     * @param object|string $class
     * @param array $options
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
                $this->getColumns($reflection),
                $options
            );
    }

    /**
     * @return QueryBuilder
     * @throws UnsupportedException
     */
    private function getQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->pdo);
    }

    /**
     * @param ReflectionObject $reflection
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
        throw new Exception('model doesnt have required table attribute');
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
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
                    $collection->push(new Field(
                        $column->getName(),
                        $column->castToType($property->getValue($model), $columnType),
                        $columnType
                    ));

                }
            }
        }
        return $collection;
    }

    /**
     * @param ReflectionClass $reflection
     * @return ColumnCollection
     */
    private function getColumns(ReflectionClass $reflection): ColumnCollection
    {
        $collection = new ColumnCollection();
        foreach ($this->columnIterator($reflection) as $column) {
            /** @var Column $column */
            $collection->push(Entity\Column::createFromAttribute($column));
        }
        return $collection;
    }

    /**
     * @param ReflectionClass $reflection
     * @return Generator
     */
    private function columnIterator(ReflectionClass $reflection): Generator
    {
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $columnAttributes = $property->getAttributes(Column::class);
            if (count($columnAttributes)) {
                foreach ($columnAttributes as $attribute) {
                    /** @var Column $column */
                    yield $attribute->newInstance();
                }
            }
        }
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     * @return ConditionCollection
     * @throws QueryBuilderException
     */
    private function getConditionsByModel(ReflectionObject $reflection, object $model): ConditionCollection
    {
        return match (true) {
            $this->hasPrimaryKey($reflection) => new ConditionCollection([$this->getPrimaryKeyValue($reflection, $model)]),
            $this->hasUnique($reflection) => new ConditionCollection([$this->getUniqueValue($reflection, $model)]),
            default => new ConditionCollection($this->buildConditionArray($reflection, $model))
        };
    }

    /**
     * @param ReflectionClass $reflection
     * @param string $option
     * @return bool
     */
    private function hasOption(ReflectionClass $reflection, string $option): bool
    {
        foreach ($this->columnIterator($reflection) as $column) {
            /** @var Column $column */
            $options = $column->getOptions();
            if ($options && in_array($option, $options, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ReflectionObject $reflection
     * @return bool
     */
    private function hasPrimaryKey(ReflectionObject $reflection): bool
    {
        return $this->hasOption($reflection, Column::PRIMARY_KEY);
    }

    /**
     * @param ReflectionObject $reflection
     * @return string
     * @throws QueryBuilderException
     */
    private function getPrimaryKeyColumnName(ReflectionObject $reflection): string
    {
        return $this->getColumnNameByOption($reflection, Column::PRIMARY_KEY);
    }

    /**
     * @param ReflectionClass $reflection
     * @param string $option
     * @return string
     * @throws QueryBuilderException
     */
    private function getColumnNameByOption(ReflectionClass $reflection, string $option): string
    {
        foreach ($this->columnIterator($reflection) as $column) {
            /** @var Column $column */
            $options = $column->getOptions();
            if ($options && in_array($option, $options, true)) {
                return $column->getName();
            }
        }
        throw new Exception('Model does not have a option ' . $option);
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     * @return Equal
     * @throws QueryBuilderException
     */
    private function getPrimaryKeyValue(ReflectionObject $reflection, object $model): Equal
    {
        $key = $this->getPrimaryKeyColumnName($reflection);
        return new Equal([$key, $model->$key]);
    }

    /**
     * @param ReflectionObject $reflection
     * @return bool
     */
    private function hasUnique(ReflectionObject $reflection): bool
    {
        return $this->hasOption($reflection, Column::UNIQUE);
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     * @return Equal
     * @throws QueryBuilderException
     */
    private function getUniqueValue(ReflectionObject $reflection, object $model): Equal
    {
        $key = $this->getFirstUniqueColumnName($reflection);
        return new Equal([$key, $model->$key]);
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     * @return array
     * @throws QueryBuilderException
     */
    private function buildConditionArray(ReflectionObject $reflection, object $model): array
    {
        $result = [];
        foreach ($this->columnIterator($reflection) as $column) {
            $key = $column->getName();
            $result[] = new Equal([
                $key,
                $model->$key
            ]);
        }
        return $result;
    }

    /**
     * @param $reflection
     * @return string
     * @throws QueryBuilderException
     */
    private function getFirstUniqueColumnName($reflection): string
    {
        return $this->getColumnNameByOption($reflection, Column::UNIQUE);
    }
}