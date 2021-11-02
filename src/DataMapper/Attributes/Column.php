<?php


namespace DataMapper\DataMapper\Attributes;

use Attribute;
use DateTime;
use DataMapper\QueryBuilder\Definitions\DefinitionInterface;

/**
 * Class Column
 * @package DataMapper\DataMapper\Attributes
 */
#[Attribute]
class Column implements DefinitionInterface
{
    /** @var string */
    public const PRIMARY_KEY = 'PRIMARY KEY';

    /** @var string */
    public const UNIQUE = 'UNIQUE KEY';

    /** @var string */
    public const AUTOINCREMENT = 'AUTO_INCREMENT';

    /** @var string */
    public const INTEGER = 'INT';

    /** @var string */
    public const BIGINT = 'BIGINT';

    /** @var string */
    public const VARCHAR = 'VARCHAR';

    /** @var string */
    public const STRING = 'VARCHAR';

    /** @var string */
    public const JSONB = 'JSONB';

    /** @var string */
    public const JSON = 'JSON';

    /** @var string */
    public const DATETIME = 'DATETIME';

    /** @var string */
    public const FLOAT = 'FLOAT';

    /** @var string */
    public const BOOLEAN = 'BOOLEAN';

    /** @var int[] */
    private const DEFAULT_LENGTH = [
        self::INTEGER => 11,
        self::VARCHAR => 255
    ];

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
        public string $name,
        public string $type,
        public array $options = []
    )
    {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    public function castToType(mixed $value, string $type): mixed
    {
        return match ($type) {
            self::INTEGER => intval($value),
            self::FLOAT => doubleval($value),
            self::STRING, self::VARCHAR => strval($value),
            self::BOOLEAN => boolval($value),
            self::JSON, self::JSONB => json_encode($value),
            self::DATETIME => $this->castDateTimeToString($value),
        };
    }

    /**
     * @param $value
     * @param $type
     * @return mixed
     */
    public function castFromType(mixed $value, string $type): mixed
    {
        return match ($type) {
            self::JSON, self::JSONB => json_decode($value, true),
        };
    }

    /**
     * @param DateTime $dateTime
     * @return string
     */
    private function castDateTimeToString(DateTime $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $definition = '`' . $this->name . '` ' . $this->type;
        $definition .= ($this->nullable) ? ' NULL ' : ' NOT NULL ';

        if ($this->length === null && array_key_exists($this->type, self::DEFAULT_LENGTH)) {
            $this->length = self::DEFAULT_LENGTH[$this->type];
        }

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