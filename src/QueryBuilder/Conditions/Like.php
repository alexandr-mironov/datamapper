<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Exceptions\Exception;
use Micro\Core\QueryBuilder\Operators;

/**
 * Class Like
 * @package unshort\core\QueryBuilder\Conditions
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