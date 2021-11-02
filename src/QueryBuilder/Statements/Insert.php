<?php


namespace Micro\Core\QueryBuilder\Statements;


use Micro\Core\QueryBuilder\{DBType, Exceptions\Exception, Expression};

/**
 * Class Insert
 * @package unshort\core\QueryBuilder\Statements
 */
class Insert implements StatementInterface
{
    /** @var bool */
    public bool $isUpdatable = false;

    /** @var bool */
    public bool $ignore = false;

    /**
     * Insert constructor.
     * @param string $tableName
     * @param array $fields
     * @param array $updatable
     */
    public function __construct(
        private string $tableName,
        private array $fields,
        private array $updatable = [],
    )
    {
        $this->isUpdatable = (bool)$this->updatable;
    }

    /**
     * @throws Exception
     */
    public function __toString(): string
    {
        $keys = array_column($this->fields, 'key');
        $columnsString = implode(',', $keys);
        $keysClone = $keys;
        array_walk($keysClone, function (&$value, $key) {
            $value = ':' . $value;
        });

        $valuesString = implode(',', array_keys($keysClone));

        $mysqlIgnore = $postgresqlIgnore = '';

        if ($this->ignore) {
            $this->isUpdatable = false;
            switch ($dbType) {
                case DBType::POSTGRESQL:
                {
                    $postgresqlIgnore = ' ON CONFLICT DO NOTHING';
                    break;
                }
                case DBType::MYSQL:
                {
                    $mysqlIgnore = 'IGNORE ';
                    break;
                }
                default:
                    throw new Exception('Invalid Database type: ' . $dbType);
            }
        }

        $query = "INSERT {$mysqlIgnore}INTO {$this->tableName} ({$columnsString}) VALUES ({$valuesString}){$postgresqlIgnore}";

        if ($this->isUpdatable) {
            $query .= $this->getUpdateStatement($dbType, $version);
        }

        return $query . ";";
    }

    /**
     * @param string|null $dbType
     * @param string|null $version
     * @return string
     * @throws Exception
     */
    private function getUpdateStatement(?string $dbType = null, ?string $version = null): string
    {
        $keysForUpdate = [];
        foreach ($this->updatable as $key => $value) {
            if ($value instanceof Expression) {
                $keysForUpdate[] = $key . ' = ' . $value;
            } else {
                $keysForUpdate[] = $key . ' = :' . $key;
            }
        }

        switch ($dbType) {
            case DBType::POSTGRESQL:
            {
                $updateStatement = ' ON CONFLICT DO UPDATE SET ';
                break;
            }
            case DBType::MYSQL:
            {
                $updateStatement = ' ON DUPLICATE KEY UPDATE SET ';
                break;
            }
            default:
                throw new Exception('Invalid Database type: ' . $dbType);
        }

        return $updateStatement . implode(',', $keysForUpdate);
    }
}