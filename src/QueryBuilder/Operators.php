<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder;

/**
 * Class Operators
 *
 * @package DataMapper\QueryBuilder
 */
class Operators
{
    // logical operators
    /** @var string logical AND */
    public const AND = 'AND';

    /** @var string logical OR */
    public const OR = 'OR';

    /** @var string logical XOR */
    public const XOR = 'XOR';

    /** @var string logical NOT */
    public const NOT = 'NOT';

    // comparison operators

    /** @var string */
    public const EQUAL = '=';

    /** @var string */
    public const NOT_EQUAL = '!=';

    /** @var string */
    public const LIKE = 'LIKE';

    /** @var string */
    public const NOT_LIKE = 'NOT LIKE';

    /** @var string */
    public const IN = 'IN';

    /** @var string */
    public const NOT_IN = 'NOT IN';

    /** @var string */
    public const BETWEEN = 'BETWEEN';

    /** @var string */
    public const GREATER_THEN = '>';

    /** @var string */
    public const NOT_LESS_THEN = '!<';

    /** @var string */
    public const LESS_THEN = '<';

    /** @var string */
    public const NOT_GREATER_THEN = '!>';

    /** @var string */
    public const GREATER_THEN_OR_EQUAL = '>=';

    /** @var string */
    public const LESS_THEN_OR_EQUAL = '<=';

    /** @var string */
    public const IS_NULL = 'IS NULL';

    /** @var string */
    public const IS_NOT_NULL = 'IS NOT NULL';

    /** @var string */
    public const EXISTS = 'EXISTS';

    /** @var string */
    public const NOT_EXISTS = 'NOT EXISTS';
}
