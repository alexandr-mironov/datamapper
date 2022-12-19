<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\MySQL\ComparisonOperators;

/**
 * Class SoundsLike
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class SoundsLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::SOUNDS_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for ' . self::CONDITION_OPERATOR . ' condition';
}
