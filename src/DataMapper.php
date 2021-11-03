<?php


namespace DataMapper;


use DataMapper\Attributes\{Column, Table};
use DataMapper\QueryBuilder\Exceptions\{Exception, Exception as QueryBuilderException, UnsupportedException};
use DataMapper\QueryBuilder\QueryBuilder;
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
    /** @var DataMapper|null */
    private static ?DataMapper $instance = null;

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
     * @return QueryBuilder
     * @throws UnsupportedException
     */
    public function find(string $className): QueryBuilder
    {
        return $this->getQueryBuilder()->find($className);
    }

    /**
     * @param object $model
     * @return int
     * @throws Exception
     * @throws UnsupportedException
     * @throws QueryBuilderException
     */
    public function store(object $model): int
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
     * @return bool
     * @throws Exception
     * @throws UnsupportedException
     */
    public function delete(object $model): bool
    {
        return $this->getQueryBuilder()
            ->delete(
                $this->getTable(new ReflectionObject($model)),
                []
            );
    }

    /**
     * @param object|string $class
     * @return bool
     * @throws Exception
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