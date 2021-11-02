<?php


namespace Micro\Core\QueryBuilder\Conditions;

use Micro\Core\QueryBuilder\Operators;

/**
 * Class NotLessThen
 * @package unshort\core\QueryBuilder\Conditions
 */
class NotLessThen extends LessThen
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_LESS_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Less Then condition';
}