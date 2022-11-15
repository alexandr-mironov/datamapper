<?php

declare(strict_types=1);

namespace DataMapper\QueryBuilder\Statements;

use DataMapper\QueryBuilder\Exceptions\Exception;
use DataMapper\QueryBuilder\Operators;

/**
 * Class WhereCollection
 *
 * @package DataMapper\QueryBuilder\Statements
 */
class WhereCollection
{
    /** @var array */
    private const ALLOWED_OPERATORS = [
        Operators::AND,
        Operators::OR,
        Operators::XOR,
    ];

    /**
     * @var array
     */
    public array $wheres = [];

    /**
     * @var string|mixed
     */
    private string $operator = Operators::AND;

    /**
     * WhereCollection constructor.
     *
     * @param array<mixed> $conditions
     */
    public function __construct(array $conditions)
    {
        $this->wheres = $this->parseConditions($conditions);
    }

    /**
     * @param array<mixed> $conditions
     *
     * @return array<string, string>
     */
    private function parseConditions(array $conditions)
    {
        $where = [];
        foreach ($conditions as $condition) {
            if ($condition instanceof WhereCollection) {
                $where[$condition->operator][] = $this->parseConditions($condition->wheres);
            }
            $where[$condition->operator][] = (string)$condition;
        }

        return $where;
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws Exception
     */
    public function __get(string $name): mixed
    {
        if (!isset($this->$name)) {
            throw new Exception('Invalid property name');
        }

        return $this->$name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->buildString($this->wheres);
    }

    /**
     * @param array<string, string> $wheres
     *
     * @return string
     */
    private function buildString(array $wheres): string
    {
        $string = '';
        foreach ($wheres as $operator => $where) {
            if (is_array($where)) {

            }
        }

        return $string;
    }
}
