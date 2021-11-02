<?php


namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\Conditions\ConditionInterface;

/**
 * Class Delete
 * @package DataMapper\QueryBuilder\Statements
 */
class Delete extends AbstractStatementWithWhere implements StatementInterface
{
    /**
     * Delete constructor.
     * @param string $table
     */
    public function __construct(private string $table)
    {

    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'DELETE FROM ' . $this->table . ' WHERE ' . $this->buildWhereStatement();
    }
}