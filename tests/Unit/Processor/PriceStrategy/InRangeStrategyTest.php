<?php

namespace App\Tests\Unit\Processor\PriceStrategy;

use App\Processor\PriceStrategy\InRangeStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Class InRangeStrategyTest
 * @package App\Tests\Unit\Processor\PriceStrategy
 */
class InRangeStrategyTest extends TestCase
{
    public function valuesProvider()
    {
        return [
            [10, 10, 10],
            [10, 11, 11],
            [10, 30, 30],
            [10, 50, 47.5],
            [10, 60, 57],
            [100, 101, 100.495],
            [100, 105, 102.9],
            [100, 115, 109.25],
            [100, 150, 142.5],
            [100, 200, 190],
            [100, 410, 390],
            [1000, 1001, 1001],
            [1000, 1005, 1005],
            [1000, 1010, 1004.95],
            [1000, 1025, 1015],
            [1000, 1200, 1180],
            [1000, 1300, 1280],
            [1000, 1500, 1480]
        ];
    }

    /**
     * @dataProvider valuesProvider
     */
    public function testNeverBecomeGreaterThanMaxAmount($a, $b, $expected)
    {
        $strategy = new InRangeStrategy();
        $actual = $strategy->process($a, $b);

        $this->assertEquals($expected, $actual);
        $this->assertLessThanOrEqual(20, $b - $actual);
    }
}