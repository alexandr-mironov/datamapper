<?php

declare(strict_types=1);

namespace DataMapper\Entity;

use DataMapper\Attributes\Column as ColumnAttribute;

class Column
{
    /**
     * @param string $key
     * @param string $type
     * @param array<mixed> $options
     */
    public function __construct(
        private string $key,
        private string $type,
        private array $options = [],
    ) {

    }

    /**
     * @param ColumnAttribute $column
     *
     * @return self
     */
    public static function createFromAttribute(ColumnAttribute $column): self
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
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
