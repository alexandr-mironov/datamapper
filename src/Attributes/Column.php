<?php

declare(strict_types=1);

namespace DataMapper\Attributes;

use Attribute;
use DataMapper\Helpers\ColumnTrait;
use DataMapper\QueryBuilder\Definitions\DefinitionInterface;
use DateTime;
use DateTimeInterface;
use Exception;

/**
 * Class Column
 *
 * @package DataMapper\Attributes
 */
#[Attribute]
class Column implements DefinitionInterface
{
    use ColumnTrait;

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
        self::VARCHAR => 255,
    ];

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
        public string $name,
        public string $type,
        public array $options = []
    ) {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function castToType(mixed $value): mixed
    {
        return match ($this->getType()) {
            self::INTEGER => intval($value),
            self::FLOAT => doubleval($value),
            self::STRING, self::VARCHAR => strval($value),
            self::BOOLEAN => boolval($value),
            self::JSON, self::JSONB => json_encode($value),
            self::DATETIME => $this->castDateTimeToString(
                $value,
                (string)($this->getOption('format') ?? 'Y-m-d H:i:s')
            ),
            default => $value,
        };
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param DateTimeInterface $dateTime
     * @param string $format
     *
     * @return string
     */
    private function castDateTimeToString(
        DateTimeInterface $dateTime,
        string $format = 'Y-m-d H:i:s'
    ): string {
        return $dateTime->format($format);
    }

    /**
     * @param string $datetimeString
     * @param string $format
     *
     * @return DateTime
     */
    private function castStringToDateTime(string $datetimeString, string $format): DateTime
    {
        return DateTime::createFromFormat($format, $datetimeString);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getOption(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }

    /**
     * @param $value
     * @param $type
     *
     * @return mixed
     */
    public function castFromType(mixed $value): mixed
    {
        return match ($this->getType()) {
            self::JSON, self::JSONB => json_decode($value, true),
            self::DATETIME => $this->castStringToDateTime(
                $value,
                (string)($this->getOption('format') ?? 'Y-m-d H:i:s')
            ),
            default => $value
        };
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString(): string
    {
        $definition = '`' . $this->name . '` ' . $this->type;
        $definition .= ($this->nullable) ? ' NULL ' : ' NOT NULL ';

        if ($this->length === null && array_key_exists($this->type, self::DEFAULT_LENGTH)) {
            $this->length = self::DEFAULT_LENGTH[$this->type];
        }

        return $this->enrichColumnDefinition($definition);
    }
}
