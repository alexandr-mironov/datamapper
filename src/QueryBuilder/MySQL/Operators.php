<?php


namespace DataMapper\QueryBuilder\MySQL;


use DataMapper\QueryBuilder\Operators as OperatorsParent;

/**
 * Class Operators
 * Operators which implemented in MySQL
 *
 * @package DataMapper\QueryBuilder\MySQL
 */
class Operators extends OperatorsParent
{
    /** @var string */
    public const RLIKE = 'RLIKE';

    /** @var string */
    public const SOUNDS_LIKE = 'SOUNDS LIKE';
}