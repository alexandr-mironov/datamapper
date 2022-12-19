<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\MySQL;

use DataMapper\QueryBuilder\MySQL\ComparisonOperators;
use DataMapper\QueryBuilder\Conditions\{Between,
    ConditionFactory as ConditionFactoryParent,
    Equal,
    Exists,
    GreaterThen,
    GreaterThenOrEqual,
    In,
    IsNotNull,
    IsNull,
    LessThen,
    LessThenOrEqual,
    Like,
    NotEqual,
    NotExists,
    NotGreaterThen,
    NotLessThen,
    Regex,
    SoundsLike};
use DataMapper\QueryBuilder\Exceptions\Exception;

/**
 * Class ConditionFactory
 *
 * @package DataMapper\QueryBuilder\MySQL
 */
class ConditionFactory extends ConditionFactoryParent
{
    /**
     * @inheritDoc
     */
    public function __construct(array $conditionParts)
    {
        [$operator, $left, $right] = $conditionParts;

        $this->condition = (string)match ($operator) {
            ComparisonOperators::EQUAL => new Equal([$left, $right]),
            ComparisonOperators::LIKE => new Like([$left, $right]),
            ComparisonOperators::BETWEEN => new Between([$left, $right, $conditionParts[3]]),
            ComparisonOperators::IN => new In([$left, $right]),
            ComparisonOperators::GREATER_THEN => new GreaterThen([$left, $right]),
            ComparisonOperators::NOT_LESS_THEN => new NotLessThen([$left, $right]),
            ComparisonOperators::LESS_THEN => new LessThen([$left, $right]),
            ComparisonOperators::NOT_GREATER_THEN => new NotGreaterThen([$left, $right]),
            ComparisonOperators::GREATER_THEN_OR_EQUAL => new GreaterThenOrEqual([$left, $right]),
            ComparisonOperators::LESS_THEN_OR_EQUAL => new LessThenOrEqual([$left, $right]),
            ComparisonOperators::NOT_EQUAL => new NotEqual([$left, $right]),
            ComparisonOperators::IS_NULL => new IsNull([$left]),
            ComparisonOperators::IS_NOT_NULL => new IsNotNull([$left]),
            ComparisonOperators::EXISTS => new Exists([$left]),
            ComparisonOperators::NOT_EXISTS => new NotExists([$left]),
            ComparisonOperators::RLIKE => new Regex([$left, $right]),
            ComparisonOperators::SOUNDS_LIKE => new SoundsLike([$left, $right]),
            default => '',
        };

        if (!$this->condition) {
            throw new Exception('Unsupported operator');
        }
    }
}
