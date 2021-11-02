<?php


namespace Micro\Core\QueryBuilder\Exceptions;

/**
 * Class Exception
 * @package unshort\core\QueryBuilder\Exceptions
 */
class Exception extends \Exception implements \JsonSerializable
{
    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        // to convert private properties too to JSON
        return get_object_vars($this);
    }
}