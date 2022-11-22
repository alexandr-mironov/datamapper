<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\PGSQL\Statements;

use DataMapper\QueryBuilder\Expression;
use DataMapper\QueryBuilder\Statements\StatementInterface;

/**
 * Class Insert
 *
 * @package DataMapper\QueryBuilder\PGSQL\Statements
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
     * @param array<mixed> $fields
     * @param string[] $updatable
     */
    public function __construct(
        private string $tableName,
        private array $fields,
        private array $updatable = [],
    ) {
        $this->isUpdatable = (bool)$this->updatable;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $keys = array_column($this->fields, 'key');
        $columnsString = implode(',', $keys);
        $keysClone = $keys;
        array_walk(
            $keysClone,
            function (&$value, $key) {
                $value = ':' . $value;
            }
        );

        $valuesString = implode(', ', $keysClone);

        $postgresqlIgnore = '';

        if ($this->ignore) {
            $this->isUpdatable = false;
            $postgresqlIgnore = ' ON CONFLICT DO NOTHING';
        }

        $query = "INSERT INTO {$this->tableName} ({$columnsString}) VALUES ({$valuesString}){$postgresqlIgnore} RETURN {$this->tableName}.id;";

        if ($this->isUpdatable) {
            $query .= $this->getUpdateStatement();
        }

        return $query;
    }

    /**
     * @return string
     */
    private function getUpdateStatement(): string
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
                $keysForUpdate[] = $key . '= :' . $key;
            }
        }

        $updateStatement = ' ON CONFLICT DO UPDATE SET ';

        return $updateStatement . implode(', ', $keysForUpdate);
    }
}
