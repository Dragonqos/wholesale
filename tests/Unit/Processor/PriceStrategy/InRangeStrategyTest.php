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
            [0, 0, 0],
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
            [100, 410, 395],
            [427, 526, 511],
            [521, 614, 599],
            [1000, 1001, 1001],
            [1000, 1005, 1005],
            [1000, 1010, 1004.95],
            [1000, 1025, 1015],
            [1000, 1200, 1185],
            [1000, 1300, 1285],
            [1000, 1500, 1485]
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