<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Operators;

/**
 * Class IsNotNull
 * @package unshort\core\QueryBuilder\Conditions
 */
class IsNotNull extends IsNull
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::IS_NOT_NULL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for IS NULL condition';
}