<?php

declare(strict_types=1);

namespace Test\units;

use DataMapper\QueryBuilder\Conditions\Between;
use DataMapper\QueryBuilder\Conditions\Equal;
use DataMapper\QueryBuilder\Conditions\Exists;
use DataMapper\QueryBuilder\Conditions\GreaterThen;
use DataMapper\QueryBuilder\Conditions\GreaterThenOrEqual;
use DataMapper\QueryBuilder\Conditions\In;
use DataMapper\QueryBuilder\Conditions\IsNotNull;
use DataMapper\QueryBuilder\Conditions\IsNull;
use DataMapper\QueryBuilder\Conditions\LessThen;
use DataMapper\QueryBuilder\Conditions\LessThenOrEqual;
use DataMapper\QueryBuilder\Exceptions\Exception;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    /**
     * @covers \DataMapper\QueryBuilder\Conditions\Between
     * @throws Exception
     */
    public function testBetween(): void
    {
        $this->assertEquals(
            "'column_name' BETWEEN 13 AND 666",
            (string)new Between(['column_name', 13, 666])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\Equal
     * @throws Exception
     */
    public function testEqual(): void
    {
        $this->assertEquals(
            "'column_name' = 13",
            (string)new Equal(['column_name', 13])
        );
        $this->assertEquals(
            "'column_name' = '13'",
            (string)new Equal(['column_name', '13'])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\Exists
     * @throws Exception
     */
    public function testExists(): void
    {
        $this->assertEquals(
            "'column_name' EXISTS",
            (string)new Exists(['column_name'])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\GreaterThen
     * @throws Exception
     */
    public function testGreaterThen(): void
    {
        $this->assertEquals(
            "'column_name' > 'value'",
            (string)new GreaterThen(['column_name', 'value'])
        );

        $this->assertEquals(
            "'column_name' > 13",
            (string)new GreaterThen(['column_name', 13])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\GreaterThenOrEqual
     * @throws Exception
     */
    public function testGreaterThenOrEqual(): void
    {
        $this->assertEquals(
            "'column_name' >= 'value'",
            (string)new GreaterThenOrEqual(['column_name', 'value'])
        );

        $this->assertEquals(
            "'column_name' >= 13",
            (string)new GreaterThenOrEqual(['column_name', 13])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\In
     * @throws Exception
     */
    public function testIn(): void
    {
        $this->assertEquals(
            "'column_name' IN ('value','0)'' OR 1=1 --',13,'''')",
            (string)new In(['column_name', ['value', "0)' OR 1=1 --", 13, "'"]])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\IsNotNull
     * @throws Exception
     */
    public function testIsNotNull(): void
    {
        $this->assertEquals(
            "'column_name' IS NOT NULL",
            (string)new IsNotNull(['column_name'])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\IsNull
     * @throws Exception
     */
    public function testIsNull(): void
    {
        $this->assertEquals(
            "'column_name' IS NULL",
            (string)new IsNull(['column_name'])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\LessThen
     * @throws Exception
     */
    public function testLessThen(): void
    {
        $this->assertEquals(
            "'column_name' < 'value'",
            (string)new LessThen(['column_name', 'value'])
        );

        $this->assertEquals(
            "'column_name' < 13",
            (string)new LessThen(['column_name', 13])
        );
    }

    /**
     * @covers \DataMapper\QueryBuilder\Conditions\LessThenOrEqual
     * @throws Exception
     */
    public function testLessThenOrEqual(): void
    {
        $this->assertEquals(
            "'column_name' <= 'value'",
            (string)new LessThenOrEqual(['column_name', 'value'])
        );

        $this->assertEquals(
            "'column_name' <= 13",
            (string)new LessThenOrEqual(['column_name', 13])
        );
    }
}
