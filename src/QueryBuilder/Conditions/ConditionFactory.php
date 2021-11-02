<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Operators;

/**
 * Class ConditionFactory
 * @package DataMapper\QueryBuilder\Conditions
 */
class ConditionFactory extends AbstractCondition
{
    /**
     * @inheritDoc
     */
    public function __construct(array $conditionParts)
    {
        [$operator, $left, $right] = $conditionParts;

        $this->condition = (string)match ($operator) {
            Operators::EQUAL => new Equal([$left, $right]),
            Operators::LIKE => new Like([$left, $right]),
            Operators::BETWEEN => new Between([$left, $right, $conditionParts[3]]),
            Operators::IN => new In([$left, $right]),
            Operators::GREATER_THEN => new GreaterThen([$left, $right]),
            Operators::NOT_LESS_THEN => new NotLessThen([$left, $right]),
            Operators::LESS_THEN => new LessThen([$left, $right]),
            Operators::NOT_GREATER_THEN => new NotGreaterThen([$left, $right]),
            Operators::GREATER_THEN_OR_EQUAL => new GreaterThenOrEqual([$left, $right]),
            Operators::LESS_THEN_OR_EQUAL => new LessThenOrEqual([$left, $right]),
            Operators::NOT_EQUAL => new NotEqual([$left, $right]),
            Operators::IS_NULL => new IsNull([$left]),
            Operators::IS_NOT_NULL => new IsNotNull([$left]),
            Operators::EXISTS => new Exists([$left]),
            Operators::NOT_EXISTS => new NotExists([$left]),
            default => '',
        };

        if (!$this->condition) {
            throw new Exception('Unsupported operator');
        }
    }
}