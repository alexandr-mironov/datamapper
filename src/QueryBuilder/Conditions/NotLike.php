<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class NotLike
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::NOT_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid condition parameters';
}
