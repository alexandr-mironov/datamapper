<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Operators;

/**
 * Class Like
 * @package DataMapper\QueryBuilder\Conditions
 */
class Like extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid condition parameters';

    /**
     * Like constructor.
     * @param array $conditionParts
     * @throws Exception
     */
    public function __construct(array $conditionParts)
    {
        [$left, $right] = $conditionParts;
        if (!isset($left, $right)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }
        parent::__construct([$left, $right]);
    }
}