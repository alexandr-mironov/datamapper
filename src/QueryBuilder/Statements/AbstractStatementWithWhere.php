<?php


namespace DataMapper\QueryBuilder\Statements;


use DataMapper\QueryBuilder\Conditions\{ConditionInterface,
    Equal,
    GreaterThen,
    GreaterThenOrEqual,
    In,
    LessThen,
    LessThenOrEqual,
    NotEqual,
    NotIn};
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

    /** @var int|null $limit */
    public ?int $limit = null;

    /** @var int|null $offset */
    public ?int $offset = null;

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
        foreach ($this->wheres as $i => $where) {
            $query .= (!$i ? '' : $where['operator']) . ' ' . $where['condition'];
        }
        return $query;
    }

    /**
     * @param int $limit
     * @param int|null $offset
     * @return $this
     */
    public function limit(int $limit, ?int $offset = null): static
    {
        $this->limit = $limit;
        if ($offset) {
            $this->offset = $offset;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
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
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
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

    /**
     * @param string $condition
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
     * @throws Exception
     */
    public function where(string $condition, string $key, mixed $value, string $operator = Operators::AND): static
    {
        if (!class_exists($condition)) {
            throw new Exception('invalid condition provided');
        }

        $conditionInstance = new $condition([$key, $value]);

        if (false === ($conditionInstance instanceof ConditionInterface)) {
            throw new Exception('invalid condition provided');
        }

        $this->addWhereCondition(
            $conditionInstance,
            $operator
        );
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
     * @throws Exception
     */
    public function whereGreaterThen(string $key, mixed $value, string $operator = Operators::AND): static
    {
        return $this->where(GreaterThen::class, $key, $value, $operator);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
     * @throws Exception
     */
    public function whereGreaterThenOrEqual(string $key, mixed $value, string $operator = Operators::AND): static
    {
        return $this->where(GreaterThenOrEqual::class, $key, $value, $operator);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
     * @throws Exception
     */
    public function whereLessThen(string $key, mixed $value, string $operator = Operators::AND): static
    {
        return $this->where(LessThen::class, $key, $value, $operator);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     * @return $this
     * @throws Exception
     */
    public function whereLessThenOrEqual(string $key, mixed $value, string $operator = Operators::AND): static
    {
        return $this->where(LessThenOrEqual::class, $key, $value, $operator);
    }
}
