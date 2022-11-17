<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Operators;

/**
 * Class Exists
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class Exists extends IsNull
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::EXISTS;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Exists condition';
}
