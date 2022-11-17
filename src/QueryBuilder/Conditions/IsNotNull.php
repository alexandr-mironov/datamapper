<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Operators;

/**
 * Class IsNotNull
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
class IsNotNull extends IsNull
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::IS_NOT_NULL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for IS NULL condition';
}
