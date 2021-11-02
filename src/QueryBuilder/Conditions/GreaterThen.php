<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class GreaterThen
 * @package unshort\core\QueryBuilder\Conditions
 */
class GreaterThen extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::GREATER_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for greater then condition';
}