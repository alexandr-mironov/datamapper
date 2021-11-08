<?php

namespace DataMapper\Entity;

use InvalidArgumentException;
use Iterator;

abstract class AbstractCollection implements Iterator
{
    public const COLLECTION_ITEM = '';

    private array $collection = [];

    private int $current = 0;

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

    public function pop(): void
    {
        unset($this->collection[count($this->collection) - 1]);
    }

    public function clear(): void
    {
        $this->collection = [];
    }

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