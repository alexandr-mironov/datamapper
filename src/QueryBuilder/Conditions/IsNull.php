<?php


namespace Micro\Core\QueryBuilder\Conditions;


use Micro\Core\QueryBuilder\Exceptions\Exception;
use Micro\Core\QueryBuilder\Operators;

/**
 * Class IsNull
 * @package unshort\core\QueryBuilder\Conditions
 */
class IsNull extends AbstractCondition
{
    /** @var string */
    protected const CONDITION_OPERATOR = Operators::IS_NULL;

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments for IS NULL condition';

    /**
     * IsNull constructor.
     * @param array $conditionParts
     * @throws Exception
     */
    public function __construct(array $conditionParts)
    {
        [$left] = $conditionParts;
        if (!$conditionParts || !isset($left)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }

        $this->condition = $this->quote($left) . ' ' . static::CONDITION_OPERATOR;
    }
}