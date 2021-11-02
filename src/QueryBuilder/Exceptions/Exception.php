<?php


namespace DataMapper\QueryBuilder\Exceptions;

/**
 * Class Exception
 * @package DataMapper\QueryBuilder\Exceptions
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