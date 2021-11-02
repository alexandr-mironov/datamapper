<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class NotEqual
 * @package unshort\core\QueryBuilder\Conditions
 */
class NotEqual extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_EQUAL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments Not Equal condition';
}