<?php

namespace DataMapper\Entity;

use DataMapper\QueryBuilder\Conditions\ConditionInterface;

/**
 * works as ConditionInterface[]
 */
class ConditionCollection extends AbstractCollection
{
    public const COLLECTION_ITEM = ConditionInterface::class;
}