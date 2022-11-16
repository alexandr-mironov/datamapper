<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\DataMapper;
use PDO;
use PHPUnit\Framework\TestCase;
use Test\TestEntity;

class SelectTest extends TestCase
{
    /**
     * @var DataMapper
     */
    private DataMapper $dataMapper;

    public function setUp(): void
    {
        $this->dataMapper = new DataMapper($this->createMock(PDO::class));
    }

    public function testSimpleQueryBuilding()
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='value'";
        $result = (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', 'value');
        $this->assertEquals($expectedSimpleQuery, $result);
    }
    
    public function testInjectionResistance()
    {
        $expectedSimpleQuery = "SELECT * FROM `some_database`.`user` WHERE 'field'='\' OR 1=1 --value\''";
        (string)$this->dataMapper
            ->find(TestEntity::class)
            ->by('field', "' OR 1=1 --value'");
        $this->assertEquals($expectedSimpleQuery, $result);
    }
}
