<?php


namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Operators;

/**
 * Class Equal
 * @package DataMapper\QueryBuilder\Conditions
 */
class Equal extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Equal condition required two elements';

    /**
     * Equal constructor.
     * @param array $conditionParts
     * @throws Exception
     */
    public function __construct(array $conditionParts)
    {
        [$left, $right] = $conditionParts;
        if (!$left || !isset($left, $right)) {
            throw new Exception(static::EXCEPTION_MESSAGE ?? 'Invalid argument');
        }
        parent::__construct([$left, $right]);
    }
}