<?php

namespace DataMapper\Entity;

class FieldCollection extends AbstractCollection
{
    public const COLLECTION_ITEM = Field::class;

    /**
     * todo: ???
     * @return string[]
     */
    public function getKeys(): array
    {
        return array_column($this->collection, 'key');
    }
}