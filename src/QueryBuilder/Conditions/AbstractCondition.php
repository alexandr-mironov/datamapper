<?php


namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Exceptions\Exception;

/**
 * Class AbstractCondition
 * @package DataMapper\QueryBuilder\Conditions
 */
abstract class AbstractCondition implements ConditionInterface
{
    /** @var string */
    protected string $condition;

    /** @var string */
    protected const CONDITION_OPERATOR = '';

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments';

    /**
     * AbstractCondition constructor.
     * @param array $conditionParts
     * @throws Exception
     */
    public function __construct(array $conditionParts)
    {
        [$left, $right] = $conditionParts;

        if (!isset($left, $right)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }

        $this->condition = $this->quote($left)
            . static::CONDITION_OPERATOR
            . $this->quote($right);
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function quote(mixed $value): string
    {
        return match (true) {
            //($value instanceof Expression), is_int($value) => $value,
            is_string($value) => "'" . $value . "'",
            default => $value,
        };
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->condition;
    }
}