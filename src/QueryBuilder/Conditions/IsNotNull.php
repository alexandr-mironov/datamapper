<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\ComparisionOperators;

/**
 * Class IsNotNull
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class IsNotNull extends IsNull
{
    /** @var string */
    protected const CONDITION_OPERATOR = ComparisionOperators::IS_NOT_NULL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for IS NULL condition';
}
