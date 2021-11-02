<?php


namespace Micro\Core\QueryBuilder\Statements;

/**
 * Class Truncate
 * @package unshort\core\QueryBuilder\Statements
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