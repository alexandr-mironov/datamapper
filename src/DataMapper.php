<?php
declare(strict_types=1);

namespace DataMapper;


use DataMapper\Attributes\{Column, Table};
use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\Exceptions\{Exception, Exception as QueryBuilderException, UnsupportedException};
use DataMapper\QueryBuilder\QueryBuilder;
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
     * @return QueryBuilder
     *
     * @throws UnsupportedException
     */
    public function find(string $className): QueryBuilder
    {
        return $this->getQueryBuilder()->find($className);
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
        $table = $this->getTable($reflection);
        $fields = $this->getFields($reflection, $model);
        $fieldsForUpdate = array_column($fields, 'key');
        if (in_array('id', $fieldsForUpdate) && false !== ($index = array_search('id', $fieldsForUpdate))) {
            unset($fieldsForUpdate[$index]);
        }

        return $this->getQueryBuilder()->insertUpdate($table, $fields, $fieldsForUpdate);
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
        $conditions = $this->getConditionsByModel($reflection, $model);
        return $this->getQueryBuilder()
            ->delete(
                $this->getTable($reflection),
                $conditions
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
     * @return string
     * @throws Exception
     */
    private function getTable(ReflectionClass $reflection): string
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
     * @return array
     */
    private function getFields(ReflectionClass $reflection, object $model): array
    {
        $result = [];
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $columnAttributes = $property->getAttributes(Column::class);
            if (count($columnAttributes)) {
                foreach ($columnAttributes as $attribute) {
                    /** @var Column $column */
                    $column = $attribute->newInstance();
                    $columnType = $column->getType();
                    $result[] = [
                        'key' => $column->getName(),
                        'value' => $column->castToType($property->getValue($model), $columnType),
                        'type' => $columnType,
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * @param ReflectionClass $reflection
     * @return array
     */
    private function getColumns(ReflectionClass $reflection): array
    {
        $result = [];
        foreach ($this->columnIterator($reflection) as $column) {
            /** @var Column $column */
            $result[] = [
                'key' => $column->getName(),
                'type' => $column->getType(),
                'options' => $column->getOptions(),
            ];
        }
        return $result;
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
     * @return ConditionInterface[]
     * @throws QueryBuilderException
     */
    private function getConditionsByModel(ReflectionObject $reflection, object $model): array
    {
        return match (true) {
            $this->hasPrimaryKey($reflection) => [$this->getPrimaryKeyValue($reflection, $model)],
            $this->hasUnique($reflection) => [$this->getUniqueValue($reflection, $model)],
            default => $this->buildConditionArray($reflection, $model)
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
     */
    private function buildConditionArray(ReflectionObject $reflection, object $model): array
    {
        return [];
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