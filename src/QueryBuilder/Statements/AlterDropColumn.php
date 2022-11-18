<?php


namespace DataMapper\QueryBuilder\Statements;

/**
 * Class AlterDropColumn
 * @package DataMapper\QueryBuilder\Statements
 */
class AlterDropColumn extends AlterOption
{
    /**
     * AlterDropColumn constructor.
     * @param string $columnName
     */
    public function __construct(
        private string $columnName
    )
    {

    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "DROP COLUMN {$this->columnName}";
    }
}
