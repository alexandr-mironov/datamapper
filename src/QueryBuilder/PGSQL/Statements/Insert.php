<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\PGSQL\Statements;

use DataMapper\QueryBuilder\Expression;
use DataMapper\QueryBuilder\Statements\Insert as InsertStatement;
use DataMapper\QueryBuilder\Statements\StatementInterface;

/**
 * Class Insert
 *
 * @package DataMapper\QueryBuilder\PGSQL\Statements
 */
class Insert extends InsertStatement implements StatementInterface
{
    /** @var bool */
    public bool $isUpdatable = false;

    /** @var bool */
    public bool $ignore = false;

    /** @var string primary key field */
    private string $returnField = 'id';

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

        $onConflict = '';

        if ($this->ignore) {
            $this->isUpdatable = false;
            $onConflict = ' ON CONFLICT DO NOTHING';
        }

        if (!$this->ignore && $this->isUpdatable) {
            $onConflict .= $this->getUpdateStatement();
        }

        return "INSERT INTO {$this->tableName} ({$columnsString}) VALUES ({$valuesString}){$onConflict} RETURN {$this->tableName}.{$this->returnField};";
    }

    public function setReturnField(string $returnField): static
    {
        $this->returnField = $returnField;

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
                $keysForUpdate[] = $value . '=:' . $value;
            }
        }

        $updateStatement = ' ON CONFLICT DO UPDATE SET ';

        return $updateStatement . implode(', ', $keysForUpdate);
    }
}
