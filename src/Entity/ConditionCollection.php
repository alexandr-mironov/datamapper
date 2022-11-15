<?php

declare(strict_types=1);

namespace DataMapper\Entity;

use DataMapper\QueryBuilder\Conditions\ConditionInterface;

/**
 * works as ConditionInterface[]
 */
class ConditionCollection extends AbstractCollection
{
    public const COLLECTION_ITEM = ConditionInterface::class;

    /**
     * ConditionCollection constructor.
     *
     * @param array<mixed> $collection
     */
    public function __construct(array $collection = [])
    {
        parent::__construct($collection);
    }
}
