<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\Entity\Field;
use DataMapper\QueryBuilder\Expression;

/**
 * Class Insert
 *
 * @package DataMapper\QueryBuilder\MySQL\Statements
 */
class Insert implements StatementInterface
{
    /** @var bool */
    public bool $isUpdatable = false;

    /** @var bool */
    public bool $ignore = false;

    /**
     * Insert constructor.
     *
     * @param string $tableName
     * @param Field[] $fields
     * @param string[] $updatable
     */
    public function __construct(
        protected string $tableName,
        protected array $fields,
        protected array $updatable = [],
    ) {
        $this->isUpdatable = (bool)$this->updatable;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $keys = array_unique(array_column($this->fields, 'key'));
        $columnsString = implode(', ', $keys);
        array_walk(
            $keys,
            function (&$value, $key) {
                $value = ':' . $value;
            }
        );

        $valuesString = implode(', ', $keys);

        $mysqlIgnore = '';

        if ($this->ignore) {
            $this->isUpdatable = false;
            $mysqlIgnore = 'IGNORE ';
        }

        $query = "INSERT {$mysqlIgnore}INTO {$this->tableName} ({$columnsString}) VALUES ({$valuesString});";

        if ($this->isUpdatable) {
            $query .= $this->getUpdateStatement();
        }

        return $query;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function ignore(bool $flag = true): static
    {
        $this->ignore = $flag;

        return $this;
    }

    /**
     * @param bool $flag
     *
     * @return $this
     */
    public function onDuplicateUpdate(bool $flag = true): static
    {
        $this->isUpdatable = $flag;

        return $this;
    }

    /**
     * @param Field ...$values
     *
     * @return $this
     */
    public function addValues(Field ...$values): static
    {
        foreach ($values as $value) {
            $this->fields[$value->getKey()] = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function getUpdateStatement(): string
    {
        $keysForUpdate = [];
        /**
         * @var string $key
         * @var string|Expression $value
         */
        foreach ($this->updatable as $key => $value) {
            if ($value instanceof Expression) {
                $keysForUpdate[] = $key . ' = ' . $value;
            } else {
                $keysForUpdate[] = $key . ' = :' . $key; // todo: replace binding values to `some_field = VALUES(some_field)`
            }
        }

        $updateStatement = ' ON DUPLICATE KEY UPDATE SET ';

        return $updateStatement . implode(',', $keysForUpdate);
    }
}
