<?php


namespace Micro\Core\QueryBuilder\Statements;


use Micro\Core\QueryBuilder\Conditions\ConditionInterface;
use Micro\Core\QueryBuilder\Exceptions\Exception;
use Micro\Core\QueryBuilder\Operators;

/**
 * Class WhereCollection
 * @package unshort\core\QueryBuilder\Statements
 */
class WhereCollection
{
    /** @var array */
    private const ALLOWED_OPERATORS = [
        Operators:: AND,
        Operators:: OR,
        Operators:: XOR,
    ];

    /**
     * @var string|mixed
     */
    private string $operator = Operators:: AND;

    /**
     * @var array
     */
    public array $wheres = [];

    /**
     * WhereCollection constructor.
     * @param array $conditions
     */
    public function __construct(array $conditions)
    {
        $this->wheres = $this->parseConditions($conditions);
    }

    /**
     * @param array $conditions
     * @return array
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
     * @param array $wheres
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

    /**
     * @param string $name
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
}