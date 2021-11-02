<?php


namespace Micro\Core\QueryBuilder\Conditions;

use Micro\Core\QueryBuilder\Operators;

/**
 * Class NotGreaterThen
 * @package unshort\core\QueryBuilder\Conditions
 */
class NotGreaterThen extends GreaterThen
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_GREATER_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Greater Then condition';
}