<?php

declare(strict_types=1);

namespace DataMapper\Helpers;

use DataMapper\Attributes\Column;
use DataMapper\Entity\ColumnCollection;
use DataMapper\Exceptions\Exception;
use Generator;
use ReflectionClass;

class ColumnHelper
{
    /**
     * @param ReflectionClass $reflection
     *
     * @return ColumnCollection
     */
    public static function getColumns(ReflectionClass $reflection): ColumnCollection
    {
        $collection = new ColumnCollection();
        foreach (self::getColumnIterator($reflection) as $propName => $column) {
            /** @var Column $column */
            $collection->addItem($propName, \DataMapper\Entity\Column::createFromAttribute($column));
        }

        return $collection;
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return string[]
     */
    public static function getFieldSet(ReflectionClass $reflection): array
    {
        $fieldSet = [];
        $prefix = $reflection->getShortName();
        /**
         * @var string $propName
         * @var Column $column
         */
        foreach (self::getColumnIterator($reflection) as $propName => $column) {
            $fieldSet[] = $column->getName() . ' as ' . $prefix . "." . $propName;
        }

        return $fieldSet;
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return Generator<string, Column>
     */
    public static function getColumnIterator(ReflectionClass $reflection): Generator
    {
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $columnAttributes = $property->getAttributes(Column::class);
            if (count($columnAttributes)) {
                foreach ($columnAttributes as $attribute) {
                    /** @var Column $column */
                    $column = $attribute->newInstance();
                    yield $property->getName() => $column;
                }
            }
        }
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return bool
     */
    public static function hasPrimaryKey(ReflectionClass $reflection): bool
    {
        return self::hasOption($reflection, Column::PRIMARY_KEY);
    }

    /**
     * @param ReflectionClass $reflection
     * @param string $option
     *
     * @return bool
     */
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

    /**
     * @param ReflectionClass $reflection
     *
     * @return bool
     */
    public static function hasUnique(ReflectionClass $reflection): bool
    {
        return self::hasOption($reflection, Column::UNIQUE);
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return string
     * @throws Exception
     */
    public static function getPrimaryKeyColumnName(ReflectionClass $reflection): string
    {
        return self::getColumnNameByOption($reflection, Column::PRIMARY_KEY);
    }

    /**
     * @param ReflectionClass $reflection
     * @param string $option
     *
     * @return string
     * @throws Exception
     */
    public static function getColumnNameByOption(ReflectionClass $reflection, string $option): string
    {
        foreach (self::getColumnIterator($reflection) as $column) {
            /** @var Column $column */
            $options = $column->getOptions();
            if ($options && in_array($option, $options, true)) {
                return $column->getName();
            }
        }
        throw new Exception('Model does not have a option ' . $option);
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return string
     * @throws Exception
     */
    public static function getFirstUniqueColumnName(ReflectionClass $reflection): string
    {
        return self::getColumnNameByOption($reflection, Column::UNIQUE);
    }
}
