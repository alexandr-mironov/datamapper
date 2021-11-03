<?php


namespace DataMapper\Attributes;


use Attribute;

/**
 * Class Table
 * @package DataMapper\Attributes
 */
#[Attribute]
class Table
{
    /**
     * Table constructor.
     * @param string $name
     * @param string $schema
     */
    public function __construct(
        public string $name,
        public string $schema = 'public'
    )
    {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return '`' . $this->schema . '`.`' . $this->name . '`';
    }
}