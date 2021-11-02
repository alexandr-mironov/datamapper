<?php


namespace Micro\Core\QueryBuilder\Statements;

/**
 * Class AlterDropColumn
 * @package unshort\core\QueryBuilder\Statements
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
    public function __toString()
    {
        return "DROP COLUMN {$this->columnName}";
    }
}