<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisonOperators;

/**
 * Class NotLike
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::NOT_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid condition parameters';
}
