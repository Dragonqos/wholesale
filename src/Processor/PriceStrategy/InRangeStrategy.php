<?php

namespace App\Processor\PriceStrategy;

/**
 * Class InRangeStrategy
 * @package App\Processor\PriceStrategy
 */
class InRangeStrategy implements PriceStrategyInterface
{
    /**
     * @var array
     */
    private $configuration = [];

    /**
     * InRangeStrategy constructor.
     */
    public function __construct()
    {
        $this->configuration = [
            [
                'operator' => 'lte',
                'inRange' => [
                    0, 3
                ],
                'wholesalePercentDiscount' => 1,
                'wholesaleMinAmount' => 0.5,
                'wholesaleMaxAmount' => 10
            ],
            [
                'operator' => 'lte',
                'inRange' => [
                    3, 7
                ],
                'wholesalePercentDiscount' => 1,
                'wholesaleMinAmount' => 2,
                'wholesaleMaxAmount' => 15,
            ],
            [
                'operator' => 'lte',
                'inRange' => [
                    7, 15
                ],
                'wholesalePercentDiscount' => 3,
                'wholesaleMinAmount' => 2,
                'wholesaleMaxAmount' => 15,
            ],
            [
                'operator' => 'lte',
                'inRange' => [
                    15, 1000
                ],
                'wholesalePercentDiscount' => 5,
                'wholesaleMinAmount' => 2,
                'wholesaleMaxAmount' => 20
            ]
        ];
    }

    /**
     * @param float $sellerCost
     * @param float $retailPrice
     *
     * @return float
     */
    public function process(float $sellerCost, float $retailPrice): float
    {
        $newSellerPrice = $retailPrice;

        foreach ($this->configuration as $settings) {
            # If operator matches
            if ($sellerCost !== $retailPrice && $this->{$settings['operator']}($sellerCost, $retailPrice)) {
                $range = $settings['inRange'];
                $diffInPercents = ($retailPrice - $sellerCost) / ($sellerCost / 100);

                # when diff between amounts is inRange
                if ($range[0] <= $diffInPercents && $diffInPercents < $range[1]) {

                    $profit = $this->getPercentOfNumber($retailPrice, $settings['wholesalePercentDiscount']);
                    $profitMinAmount = $settings['wholesaleMinAmount'] || 0.5;

                    $profit = max($profit, $profitMinAmount);

                    $profitMaxAmount = $settings['wholesaleMaxAmount'] || 1;
                    $profit = min($profit, $profitMaxAmount);

                    # Round half to up
                    $profit = round($profit);

                    $newSellerPrice = $retailPrice - $profit;
                }
            }
        }

        return $newSellerPrice;
    }

    /**
     * @param float $sellerCost
     * @param       $retailPrice
     *
     * @return bool
     */
    private function eq(float $sellerCost, float $retailPrice): bool
    {
        return $sellerCost === $retailPrice;
    }

    /**
     * @param float $sellerCost
     * @param       $retailPrice
     *
     * @return bool
     */
    private function lte(float $sellerCost, $retailPrice): bool
    {
        return $sellerCost <= $retailPrice;
    }

    /**
     * @param float $number
     * @param float $percent
     *
     * @return float
     */
    private function getPercentOfNumber(float $number, float $percent): float
    {
        return ($percent / 100) * $number;
    }

}