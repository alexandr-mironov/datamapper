<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Operators;

/**
 * Class In
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class In extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::IN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid right hand of IN condition';

    /**
     * @inheritDoc
     */
    public function __construct(array $conditionParts)
    {
        [$left, $right] = $conditionParts;
        if (!is_array($right)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }
        foreach ($right as &$el) {
            $el = $this->quote($el);
        }

        $this->condition = $this->glueParts(
            $this->quote($left),
            static::CONDITION_OPERATOR,
            '(' . implode(',', $right) . ')'
        );
    }
}
