<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class LessThen
 * @package unshort\core\QueryBuilder\Conditions
 */
class LessThen extends Equal
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::LESS_THEN;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Less Then condition';
}