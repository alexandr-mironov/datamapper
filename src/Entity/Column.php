<?php

declare(strict_types=1);

namespace DataMapper\Entity;

class Column
{
    /**
     * @param string $key
     * @param string $type
     * @param array $options
     */
    public function __construct(
        private string $key,
        private string $type,
        private array  $options = [],
    )
    {

    }

    /**
     * @param \DataMapper\Attributes\Column $column
     *
     * @return static
     */
    public static function createFromAttribute(\DataMapper\Attributes\Column $column): self
    {
        return new self(
            $column->getName(),
            $column->getType(),
            $column->getOptions(),
        );
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
    public function getOptions(): array
    {
        return $this->options;
    }
}