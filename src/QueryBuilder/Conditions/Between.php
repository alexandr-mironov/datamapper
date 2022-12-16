<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;
use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\LogicalOperators;

/**
 * Class Between
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class Between extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::BETWEEN;

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
        $this->condition = $this->glueParts(
            $this->quote($value),
            static::CONDITION_OPERATOR,
            $this->quote($from),
            LogicalOperators:: AND,
            $this->quote($to)
        );
    }
}
