<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class NotExists
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class NotExists extends Exists
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::NOT_EXISTS;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Exists condition';
}
