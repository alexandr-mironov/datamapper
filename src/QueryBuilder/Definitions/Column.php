<?php


namespace DataMapper\QueryBuilder\Definitions;

/**
 * Class Column
 * @package DataMapper\QueryBuilder
 *
 *
 */
class Column implements DefinitionInterface
{
    /** @var int|null */
    public ?int $length = null;

    /** @var bool */
    public bool $nullable = true;

    /** @var mixed|null */
    public mixed $default = null;

    /**
     * Column constructor.
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function __construct(
        private string $name,
        private string $type,
        private array $options = [],
    )
    {

    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $definition = '`' . $this->name . '` ' . $this->type;
        $definition .= ($this->nullable) ? ' NULL ' : ' NOT NULL ';
        if ($this->length) {
            $definition .= '(' . $this->length . ')';
        }

        if (!is_null($this->default)) {
            $definition .= ' DEFAULT ' . $this->default;
        }

        if ($this->options) {
            $definition .= ' ' . implode(' ', $this->options);
        }

        return $definition;
    }
}