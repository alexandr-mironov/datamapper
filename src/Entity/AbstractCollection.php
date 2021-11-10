<?php

namespace DataMapper\Entity;

use InvalidArgumentException;
use Iterator;

abstract class AbstractCollection implements Iterator
{
    /** @var string */
    public const COLLECTION_ITEM = '';

    /** @var array $collection */
    protected array $collection = [];

    /** @var int $current current item key (index) */
    protected int $current = 0;

    /**
     * @param array $collection
     */
    public function __construct(array $collection = [])
    {
        foreach ($collection as $item) {
            if (false === ($item instanceof static::COLLECTION_ITEM)) {
                throw new InvalidArgumentException(
                    'This collection expect '
                    . static::COLLECTION_ITEM
                    . ' as item, '
                    . $item::class
                    . ' provided'
                );
            }
            $this->collection[] = $item;
        }
    }

    /**
     * Add item to end of collection
     *
     * @param object $item
     */
    public function push(object $item): void
    {
        if (false === ($item instanceof static::COLLECTION_ITEM)) {
            throw new InvalidArgumentException(
                'This collection expect '
                . static::COLLECTION_ITEM
                . ' as item, '
                . $item::class
                . ' provided'
            );
        }
        $this->collection[] = $item;
    }

    /**
     * Remove last element of collection
     */
    public function pop(): void
    {
        unset($this->collection[count($this->collection) - 1]);
    }

    /**
     * Clear collection
     */
    public function clear(): void
    {
        $this->collection = [];
    }

    /**
     * @return object
     */
    public function current(): object
    {
        return $this->collection[$this->current];
    }

    public function next(): void
    {
        ++$this->current;

    }

    public function key(): int
    {
        return $this->current;
    }

    public function valid(): bool
    {
        return array_key_exists($this->current, $this->collection);
    }

    public function rewind(): void
    {
        $this->current = 0;
    }
}