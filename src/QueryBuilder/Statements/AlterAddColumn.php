<?php


namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\Definitions\Column;

/**
 * Class AlterAddColumn
 * @package DataMapper\QueryBuilder\Statements
 */
class AlterAddColumn extends AlterOption
{
    /**
     * AlterAddColumn constructor.
     * @param string $columnName
     * @param Column $columnDefinition
     */
    protected function __construct(
        private string $columnName,
        private Column $columnDefinition
    )
    {

    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "ADD COLUMN {$this->columnName} {$this->columnDefinition}";
    }
}