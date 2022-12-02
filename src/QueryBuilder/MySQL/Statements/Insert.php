<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\MySQL\Statements;

use DataMapper\QueryBuilder\Expression;
use DataMapper\QueryBuilder\Statements\StatementInterface;

/**
 * Class Insert
 *
 * @package DataMapper\QueryBuilder\MySQL\Statements
 */
class Insert extends \DataMapper\QueryBuilder\Statements\Insert implements StatementInterface
{
    /** @var bool */
    public bool $isUpdatable = false;

    /** @var bool */
    public bool $ignore = false;

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
