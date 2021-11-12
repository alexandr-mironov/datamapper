<?php

namespace DataMapper\Entity;

class Field
{
    /**
     * @param string $key
     * @param mixed $value
     * @param string $type
     */
    public function __construct(
        private string $key,
        private mixed  $value,
        private string $type,
    )
    {

    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}