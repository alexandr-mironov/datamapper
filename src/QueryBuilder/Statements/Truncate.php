<?php


namespace DataMapper\QueryBuilder\Statements;

/**
 * Class Truncate
 * @package DataMapper\QueryBuilder\Statements
 */
class Truncate implements StatementInterface
{
    /**
     * Truncate constructor.
     * @param string $table
     */
    public function __construct(
        private string $table
    )
    {

    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return "TRUNCATE $this->table";
    }
}