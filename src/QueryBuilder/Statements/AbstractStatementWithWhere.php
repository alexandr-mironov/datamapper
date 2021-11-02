<?php


namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\Conditions\ConditionInterface;

/**
 * Class AbstractStatementWithWhere
 * @package DataMapper\QueryBuilder\Statements
 */
abstract class AbstractStatementWithWhere
{
    /** @var array */
    public array $wheres = [];

    /**
     * @param ConditionInterface $where
     */
    public function addWhereCondition(ConditionInterface $where)
    {
        $this->wheres[] = [
            'operator' => null,
            'condition' => (string)$where,
        ];
    }

    /**
     * @return string
     */
    public function buildWhereStatement(): string
    {
        $query = '';
        foreach ($this->wheres as $where) {
            $query .= $where['condition'];
        }
        return $query;
    }
}