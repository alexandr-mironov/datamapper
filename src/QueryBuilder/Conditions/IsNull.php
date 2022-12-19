<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;
use DataMapper\QueryBuilder\Exceptions\Exception;

/**
 * Class IsNull
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class IsNull extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::IS_NULL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for IS NULL condition';

    /**
     * IsNull constructor.
     *
     * @param array<mixed> $conditionParts
     *
     * @throws Exception
     */
    public function __construct(array $conditionParts)
    {
        [$left] = $conditionParts;
        if (!$conditionParts || !isset($left)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }

        $this->condition = $this->glueParts($this->quote($left), static::CONDITION_OPERATOR);
    }
}
