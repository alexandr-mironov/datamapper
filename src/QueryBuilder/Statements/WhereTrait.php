<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\QueryBuilder\Conditions\ConditionInterface;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\Conditions\GreaterThen;
use DataMapper\QueryBuilder\Conditions\GreaterThenOrEqual;
use DataMapper\QueryBuilder\Conditions\In;
use DataMapper\QueryBuilder\Conditions\LessThen;
use DataMapper\QueryBuilder\Conditions\LessThenOrEqual;
use DataMapper\QueryBuilder\Conditions\NotEqual;
use DataMapper\QueryBuilder\Conditions\NotIn;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\LogicalOperators;

trait WhereTrait
{
    /** @var array<array{operator: string, condition: string}> */
    public array $wheres = [];

    /** @var int|null $limit */
    public ?int $limit = null;

    /** @var int|null $offset */
    public ?int $offset = null;

    /** @var array<mixed>  */
    public array $order = [];

    /**
     * @return string
     */
    protected function buildWhereStatement(): string
    {
        $query = '';
        foreach ($this->wheres as $i => $where) {
            $query .= (!$i ? '' : ' ' . $where['operator'] . ' ') . $where['condition'];
        }

        return $query;
    }

    /**
     * @param int $limit
     * @param int|null $offset
     *
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
     * @param string|array<mixed> $order
     *
     * @return $this
     */
    public function order(string|array $order): static
    {
        switch (true) {
            case is_string($order):
                $this->order[] = [
                    $order => 'DESC',
                ];
                break;
            default:
                $this->order = $order;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     *
     * @return $this
     * @throws Exception
     */
    public function by(string $key, mixed $value, string $operator = LogicalOperators:: AND): static
    {
        $args = [$key, $value];
        $this->addWhereCondition(
            (is_array($value)) ? new In($args) : new Equal($args),
            $operator
        );

        return $this;
    }

    /**
     * @param ConditionInterface $condition
     * @param string $operator
     *
     * @return WhereTrait
     */
    public function addWhereCondition(ConditionInterface $condition, string $operator = LogicalOperators:: AND): static
    {
        $this->wheres[] = [
            'operator' => $operator,
            'condition' => (string)$condition,
        ];

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     *
     * @return $this
     * @throws Exception
     */
    public function byNot(string $key, mixed $value, string $operator = LogicalOperators:: AND): static
    {
        $args = [$key, $value];
        $this->addWhereCondition(
            (is_array($value)) ? new NotIn($args) : new NotEqual($args),
            $operator
        );

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     *
     * @return $this
     * @throws Exception
     */
    public function whereGreaterThen(string $key, mixed $value, string $operator = LogicalOperators:: AND): static
    {
        return $this->where(GreaterThen::class, $key, $value, $operator);
    }

    /**
     * @param string $condition
     * @param string $key
     * @param mixed $value
     * @param string $operator
     *
     * @return $this
     * @throws Exception
     */
    public function where(string $condition, string $key, mixed $value, string $operator = LogicalOperators:: AND): static
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
     *
     * @return $this
     * @throws Exception
     */
    public function whereGreaterThenOrEqual(string $key, mixed $value, string $operator = LogicalOperators:: AND): static
    {
        return $this->where(GreaterThenOrEqual::class, $key, $value, $operator);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     *
     * @return $this
     * @throws Exception
     */
    public function whereLessThen(string $key, mixed $value, string $operator = LogicalOperators:: AND): static
    {
        return $this->where(LessThen::class, $key, $value, $operator);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $operator
     *
     * @return $this
     * @throws Exception
     */
    public function whereLessThenOrEqual(string $key, mixed $value, string $operator = LogicalOperators:: AND): static
    {
        return $this->where(LessThenOrEqual::class, $key, $value, $operator);
    }
}
