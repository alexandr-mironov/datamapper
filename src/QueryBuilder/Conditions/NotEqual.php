<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;

/**
 * Class NotEqual
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::NOT_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments Not Equal condition';
}
