<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class Exists
 * @package unshort\core\QueryBuilder\Conditions
 */
class Exists extends IsNull
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::EXISTS;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for Exists condition';
}