<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

/**
 * Class DropIndex
 *
 * @package DataMapper\QueryBuilder\Statements
 *
 * DROP INDEX index_name ON tbl_name
 * [algorithm_option | lock_option] ...
 *
 * algorithm_option:
 * ALGORITHM [=] {DEFAULT | INPLACE | COPY}
 *
 * lock_option:
 * LOCK [=] {DEFAULT | NONE | SHARED | EXCLUSIVE}
 */
class DropIndex implements StatementInterface
{
    /**
     * DropIndex constructor.
     */
    public function __construct(
        private string $indexName,
        private string $tableName,
    ) {

    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return "DROP INDEX {$this->indexName} ON {$this->tableName};";
    }
}
