<?php


namespace DataMapper\QueryBuilder\Conditions;


use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Operators;

/**
 * Class Between
 * @package DataMapper\QueryBuilder\Conditions
 */
class Between extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::BETWEEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid condition parameters';

    /**
     * @inheritDoc
     */
    public function __construct(array $conditionParts)
    {
        [$value, $from, $to] = $conditionParts;
        if (!isset($value, $from, $to)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }
        $this->condition = $this->quote($value)
            . ' '
            . static::CONDITION_OPERATOR
            . ' '
            . $this->quote($from)
            . ' '
            . Operators:: AND
            . ' '
            . $this->quote($to);
    }
}