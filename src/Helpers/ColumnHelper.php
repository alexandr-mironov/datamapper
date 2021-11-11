<?php

namespace DataMapper\Helpers;

use DataMapper\Attributes\Column;
use DataMapper\Entity\ColumnCollection;
use DataMapper\Entity\ConditionCollection;
use DataMapper\QueryBuilder\Exceptions\Exception as QueryBuilderException;
use Generator;
use ReflectionClass;
use ReflectionObject;

class ColumnHelper
{
    /**
     * @param ReflectionClass $reflection
     * @return ColumnCollection
     */
    public static function getColumns(ReflectionClass $reflection): ColumnCollection
    {
        $collection = new ColumnCollection();
        foreach (self::getColumnIterator($reflection) as $column) {
            /** @var Column $column */
            $collection->push(\DataMapper\Entity\Column::createFromAttribute($column));
        }
        return $collection;
    }

    public static function hasOption(ReflectionClass $reflection, string $option): bool
    {
        foreach (self::getColumnIterator($reflection) as $column) {
            /** @var Column $column */
            $options = $column->getOptions();
            if ($options && in_array($option, $options, true)) {
                return true;
            }
        }
        return false;
    }

    public static function getColumnIterator(ReflectionClass $reflection): Generator
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
     * @return bool
     */
    public static function hasPrimaryKey(ReflectionObject $reflection): bool
    {
        return self::hasOption($reflection, Column::PRIMARY_KEY);
    }

    /**
     * @param ReflectionObject $reflection
     * @param object $model
     * @return ConditionCollection
     */
    public static function getConditionsByModel(ReflectionObject $reflection, object $model): ConditionCollection
    {
        return match (true) {
            self::hasPrimaryKey($reflection) => new ConditionCollection([self::getPrimaryKeyValue($reflection, $model)]),
            self::hasUnique($reflection) => new ConditionCollection([self::getUniqueValue($reflection, $model)]),
            default => new ConditionCollection(self::buildConditionArray($reflection, $model))
        };
    }
}