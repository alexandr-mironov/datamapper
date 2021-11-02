<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class NotIn
 * @package unshort\core\QueryBuilder\Conditions
 */
class NotIn extends In
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_IN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid right hand of NOT IN condition';
}