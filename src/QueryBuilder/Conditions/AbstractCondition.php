<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

use DataMapper\QueryBuilder\Exceptions\Exception;

/**
 * Class AbstractCondition
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
abstract class AbstractCondition implements ConditionInterface
{
    /** @var string */
    protected const CONDITION_OPERATOR = '';

    /** @var string */
    protected const EXCEPTION_MESSAGE = 'Invalid arguments';

    /** @var string */
    protected string $condition;

    public string $separator = ' ';

    /**
     * AbstractCondition constructor.
     *
     * @param array<mixed> $conditionParts
     *
     * @throws Exception
     */
    public function __construct(array $conditionParts)
    {
        [$left, $right] = $conditionParts;

        if (!isset($left, $right)) {
            throw new Exception(static::EXCEPTION_MESSAGE);
        }

        $this->condition = $this->glueParts(
            $this->quote($left),
            static::CONDITION_OPERATOR,
            $this->quote($right)
        );
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function quote(mixed $value): mixed
    {
        return match (true) {
            //($value instanceof Expression), is_int($value) => $value,
            is_string($value) => $this->quoteValue($value),
            default => $value,
        };
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function quoteValue(string $value): string
    {
        return "'"
            . addcslashes(
                str_replace("'", "''", $value),
                "\000\n\r\\\032"
            )
            . "'";
    }

    /**
     * @param mixed ...$args
     *
     * @return string
     */
    protected function glueParts(...$args): string
    {
        return implode($this->separator, $args);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->condition;
    }
}
