<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\MySQL\ComparisonOperators;

class Regex extends Like
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisonOperators::RLIKE;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for ' . self::CONDITION_OPERATOR . ' condition';
}
