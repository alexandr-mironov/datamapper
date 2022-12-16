<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;
use DataMapper\QueryBuilder\Exceptions\Exception;

/**
 * Class ConditionFactory
 *
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
            ComparisionOperators::EQUAL => new Equal([$left, $right]),
            ComparisionOperators::LIKE => new Like([$left, $right]),
            ComparisionOperators::BETWEEN => new Between([$left, $right, $conditionParts[3]]),
            ComparisionOperators::IN => new In([$left, $right]),
            ComparisionOperators::GREATER_THEN => new GreaterThen([$left, $right]),
            ComparisionOperators::NOT_LESS_THEN => new NotLessThen([$left, $right]),
            ComparisionOperators::LESS_THEN => new LessThen([$left, $right]),
            ComparisionOperators::NOT_GREATER_THEN => new NotGreaterThen([$left, $right]),
            ComparisionOperators::GREATER_THEN_OR_EQUAL => new GreaterThenOrEqual([$left, $right]),
            ComparisionOperators::LESS_THEN_OR_EQUAL => new LessThenOrEqual([$left, $right]),
            ComparisionOperators::NOT_EQUAL => new NotEqual([$left, $right]),
            ComparisionOperators::IS_NULL => new IsNull([$left]),
            ComparisionOperators::IS_NOT_NULL => new IsNotNull([$left]),
            ComparisionOperators::EXISTS => new Exists([$left]),
            ComparisionOperators::NOT_EXISTS => new NotExists([$left]),
            default => '',
        };

        if (!$this->condition) {
            throw new Exception('Unsupported operator');
        }
    }
}
