<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;

/**
 * Class GreaterThenOrEqual
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class GreaterThenOrEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::GREATER_THEN_OR_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Greater Then Or Equal condition';
}
