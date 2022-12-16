<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

class LogicalOperators
{
    /** @var string logical OR */
    public const OR = 'OR';

    /** @var string logical NOT */
    public const NOT = 'NOT';

    /** @var string logical AND */
    public const AND = 'AND';

    /** @var string logical XOR */
    public const XOR = 'XOR';
}
