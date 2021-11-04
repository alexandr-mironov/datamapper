<?php


namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\Conditions\In;
use DataMapper\QueryBuilder\Conditions\NotEqual;
use DataMapper\QueryBuilder\Conditions\NotIn;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Operators;

/**
 * Class AbstractStatementWithWhere
 * @package DataMapper\QueryBuilder\Statements
 */
abstract class AbstractStatementWithWhere
{
    /** @var array */
    public array $wheres = [];

    /**
     * @param ConditionInterface $condition
     * @param string $operator
     */
    public function addWhereCondition(ConditionInterface $condition, string $operator = Operators::AND)
    {
        $this->wheres[] = [
            'operator' => $operator,
            'condition' => (string)$condition,
        ];
    }

    /**
     * @return string
     */
    public function buildWhereStatement(): string
    {
        $query = '';
        foreach ($this->wheres as $where) {
            $query .= $where['operator'] . ' ' . $where['condition'];
        }
        return $query;
    }

    public function limit(int $limit, ?int $offset = null): static
    {
        $this->limit = $limit;
        if ($offset) {
            $this->offset = $offset;
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function by(string $key, mixed $value, string $operator = Operators::AND): static
    {
        $args = [$key, $value];
        $this->addWhereCondition(
            (is_array($value)) ? new In($args) : new Equal($args),
            $operator
        );
        return $this;
    }

    /**
     * @throws Exception
     */
    public function byNot(string $key, mixed $value, string $operator = Operators::AND): static
    {
        $args = [$key, $value];
        $this->addWhereCondition(
            (is_array($value)) ? new NotIn($args) : new NotEqual($args),
            $operator
        );
        return $this;
    }
}