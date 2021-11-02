<?php


namespace Micro\Core\QueryBuilder\MySQL;


use PDO;
use Micro\Core\QueryBuilder\Exceptions\Exception;
use Micro\Core\QueryBuilder\PGSQL\Statements\Insert;
use Micro\Core\QueryBuilder\QueryBuilder as ParentQueryBuilder;

/**
 * Class QueryBuilder
 * @package unshort\core\QueryBuilder\MySQL
 */
final class QueryBuilder extends ParentQueryBuilder
{
    /**
     * QueryBuilder constructor.
     * @param PDO $pdo
     */
    protected function __construct(private PDO $pdo)
    {

    }

    /**
     * @param string $table
     * @param array $values
     * @return int
     * @throws Exception
     */
    public function insert(string $table, array $values): int
    {
        $insertStatement = new Insert($table, $values);
        $statement = $this->pdo->query((string)$insertStatement);
        foreach ($values as $value) {
            $statement->bindParam($value['key'], $value['value'], $this->getType($value['type']));
        }

        if (!$result = $statement->execute()) {
            throw new Exception('Invalid query ' . $insertStatement);
        }

        return (int)$this->pdo->lastInsertId();
    }
}