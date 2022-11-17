<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Conditions;

/**
 * Interface ConditionInterface
 *
 * @package DataMapper\QueryBuilder\Conditions
 */
interface ConditionInterface
{
    /**
     * @return string
     */
    public function __toString(): string;
}
