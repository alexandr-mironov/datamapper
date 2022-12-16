<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\MySQL\ComparisionOperators;

/**
 * Class SoundsLike
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class SoundsLike extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::SOUNDS_LIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for ' . self::CONDITION_OPERATOR . ' condition';
}
