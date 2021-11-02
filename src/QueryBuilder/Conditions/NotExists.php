<?php


namespace Micro\Core\QueryBuilder\Conditions;

use Micro\Core\QueryBuilder\Operators;

/**
 * Class NotExists
 * @package unshort\core\QueryBuilder\Conditions
 */
class NotExists extends Exists
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::NOT_EXISTS;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid argument for Not Exists condition';
}