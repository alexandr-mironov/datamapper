<?php


namespace Micro\Core\DataMapper;


use PDO;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use Micro\Core\App;
use Micro\Core\DataMapper\Attributes\{Column, Table};
use Micro\Core\QueryBuilder\Exceptions\{Exception as QueryBuilderException, UnsupportedException};
use Micro\Core\QueryBuilder\QueryBuilder;
use Micro\Exceptions\Exception;

/**
 * Class DataMapper
 * @package Micro\Core\DataMapper
 */
class DataMapper
{
    /** @var DataMapper|null */
    private static ?DataMapper $instance = null;

    /**
     * DataMapper constructor.
     * @param PDO $pdo
     */
    private function __construct(
        private PDO $pdo,
    )
    {

    }

    /**
     * @return static
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new static(App::getInstance()->getPdo());
        }

        return self::$instance;
    }

    /**
     * @param string $className
     * @return QueryBuilder
     * @throws UnsupportedException
     */
    public static function find(string $className): QueryBuilder
    {
        $queryBuilder = self::getQueryBuilder();
        $queryBuilder->find($className);
        return $queryBuilder;
    }

    /**
     * @param object $model
     * @return int
     * @throws Exception
     * @throws UnsupportedException
     * @throws QueryBuilderException
     */
    public static function store(object $model): int
    {
        $self = self::getInstance();
        $reflection = new ReflectionObject($model);
        $table = $self->getTable($reflection);
        $fields = $self->getFields($reflection, $model);
        $fieldsForUpdate = array_column($fields, 'key');
        if (in_array('id', $fieldsForUpdate) && false !== ($index = array_search('id', $fieldsForUpdate))) {
            unset($fieldsForUpdate[$index]);
        }

        return self::getQueryBuilder()->insertUpdate($table, $fields, $fieldsForUpdate);
    }

    /**
     * @param object $model
     * @return bool
     * @throws Exception
     * @throws UnsupportedException
     */
    public static function delete(object $model): bool
    {
        $self = self::getInstance();
        $reflection = new ReflectionObject($model);
        $table = $self->getTable($reflection);
        return self::getQueryBuilder()->delete($table, []);
    }

    /**
     * @param object|string $class
     * @return bool
     * @throws Exception
     * @throws ReflectionException
     * @throws UnsupportedException
     */
    public static function createTable(object|string $class): bool
    {
        $self = self::getInstance();
        $reflection = new ReflectionClass((is_object($class)) ? $class::class : $class);
        $queryBuilder = self::getQueryBuilder();
        return $queryBuilder->createTable($self->getTable($reflection), $self->getColumns($reflection));
    }

    /**
     * @return QueryBuilder
     * @throws UnsupportedException
     */
    private static function getQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder(static::$instance->pdo);
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
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $columnAttributes = $property->getAttributes(Column::class);
            if (count($columnAttributes)) {
                foreach ($columnAttributes as $attribute) {
                    /** @var Column $column */
                    $column = $attribute->newInstance();
                    $result[] = [
                        'key' => $column->getName(),
                        'type' => $column->getType(),
                        'options' => $column->getOptions(),
                    ];
                }
            }
        }
        return $result;
    }
}