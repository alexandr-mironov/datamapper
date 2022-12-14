<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Definitions;

use DataMapper\Helpers\ColumnTrait;
use Exception;

/**
 * Class Column
 *
 * @package DataMapper\QueryBuilder
 */
class Column implements DefinitionInterface
{
    use ColumnTrait;

    /** @var int|null */
    public ?int $length = null;

    /** @var bool */
    public bool $nullable = true;

    /** @var mixed|null */
    public mixed $default = null;

    /**
     * Column constructor.
     *
     * @param string $name
     * @param string $type
     * @param array<mixed> $options
     */
    public function __construct(
        private string $name,
        private string $type,
        private array $options = [],
    ) {

    }

    /**
     * @param array<mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function __toString(): string
    {
        $definition = '`' . $this->name . '` ' . $this->type;
        $definition .= ($this->nullable) ? ' NULL ' : ' NOT NULL ';

        return $this->enrichColumnDefinition($definition);
    }
}
