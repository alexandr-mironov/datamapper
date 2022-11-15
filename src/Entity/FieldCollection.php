<?php

declare(strict_types=1);

namespace DataMapper\Entity;

class FieldCollection extends AbstractCollection
{
    public const COLLECTION_ITEM = Field::class;

    /**
     * FieldCollection constructor.
     *
     * @param array<mixed> $collection
     */
    public function __construct(array $collection = [])
    {
        parent::__construct($collection);
    }

    /**
     * todo: ???
     *
     * @return string[]
     */
    public function getKeys(): array
    {
        return array_column($this->collection, 'key');
    }
}
