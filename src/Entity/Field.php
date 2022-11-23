<?php

declare(strict_types=1);

namespace DataMapper\Entity;

class Field
{
    /**
     * @param string $key
     * @param mixed $value
     * @param string $type
     */
    public function __construct(
        public string $key,
        public mixed $value,
        public string $type,
    ) {

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
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
