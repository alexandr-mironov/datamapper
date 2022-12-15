<?php

declare(strict_types=1);

namespace DataMapper\Entity;

use Generator;
use InvalidArgumentException;
use Iterator;

/**
 * Class Collection
 *
 * @package DataMapper\Entity
 *
 * This collection has main value, and can have many key maps,
 * that's mean you can create map (based on this collection) by choosing one of any unique value in collection
 * for example:
 *  we have some data as
 *      | id | slug  | ... other properties |
 *      |  1 | alpha | .................... |
 *      |  2 | beta  | .................... |
 *
 *  we create collection
 *      ```php
 *          $collection = new Collection(SomeCLass::class, 'id', 'slug');
 *          foreach ($iterator as $item) {
 *              $instance = new SomeClass($item); // just example of entity creating
 *              $collection->push($instance);
 *          }
 *
 *          // after that you can get map by passing name of key
 *          $idMappedCollection = $collection->getMap('id'); // array with ID as key and SomeClass entity as value
 *          $slugMappedCollection = $collection->getMap('slug'); // array with SLUG as key and SomeClass entity as value
 *      ```
 */
class Collection implements Iterator
{
    /** @var object[] $collection */
    protected array $collection = [];

    /** @var int $current current item key (index) */
    protected int $current = 0;

    /**
     * @var array<string, array<object>>
     */
    protected array $keyMaps = [];

    /**
     * Collection constructor.
     *
     * @param string $entityClass
     * @param string ...$keys
     */
    public function __construct(
        protected string $entityClass,
        string ...$keys
    ) {
        foreach ($keys as $key) {
            if (!property_exists($this->entityClass, $key)) {
                // todo: replace Exception to some custom exception
                throw new InvalidArgumentException(
                    'Property '
                    . $key
                    . ' not exists at entity '
                    . $this->entityClass
                );
            }
            $this->keyMaps[$key] = [];
        }
    }

    public function validateItem(object $item): bool
    {
        return $item instanceof $this->entityClass;
    }

    /**
     * @param object $item
     */
    public function push(object $item): void
    {
        if (!$this->validateItem($item)) {
            // todo: replace Exception to some custom exception
            throw new InvalidArgumentException('Provided item is not instance of ' . $this->entityClass);
        }

        $nextIndex = count($this->collection) + 1;
        $this->collection[$nextIndex] = $item;

        foreach ($this->keyMaps as $mapKey => &$collection) {
            $collection[(string)$item->$mapKey] = &$item; // ?object was set as link anyway?
        }
    }

    /**
     * @param string $key
     *
     * @return object[]
     */
    public function getMap(string $key): array
    {
        if (!array_key_exists($key, $this->keyMaps)) {
            // todo: replace Exception to some custom exception
            throw new InvalidArgumentException("Collection can't be mapped with key " . $key);
        }

        return $this->keyMaps[$key];
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

    /**
     * @param string $key
     *
     * @return Generator
     */
    public function getMapIterator(string $key): Generator
    {
        foreach ($this->collection as $item) {
            if (!property_exists($item, $key)) {
                // todo: replace Exception to some custom exception
                throw new InvalidArgumentException("Item doesn't have property " . $key);
            }
            yield ((string)$item->{$key}) => $item;
        }
    }
}
