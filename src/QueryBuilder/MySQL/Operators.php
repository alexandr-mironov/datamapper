<?php


namespace Micro\Core\QueryBuilder\MySQL;


use Micro\Core\QueryBuilder\Operators as OperatorsParent;

/**
 * Class Operators
 * Operators which implemented in MySQL
 *
 * @package unshort\core\QueryBuilder\MySQL
 */
class Operators extends OperatorsParent
{
    /** @var string */
    public const RLIKE = 'RLIKE';

    /** @var string */
    public const SOUNDS_LIKE = 'SOUNDS LIKE';
}