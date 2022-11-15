<?php

declare(strict_types=1);

namespace DataMapper\Entity;

class ColumnCollection extends AbstractCollection
{
    public const COLLECTION_ITEM = Column::class;

    /**
     * ColumnCollection constructor.
     *
     * @param array<mixed> $collection
     */
    public function __construct(array $collection = [])
    {
        parent::__construct($collection);
    }
}
