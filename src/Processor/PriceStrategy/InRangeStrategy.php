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
                'inRange' => [
                    0, 3
                ],
                'discount' => 1,
                'minDiscount' => 0.5,
                'maxAmount' => 10
            ],
            [
                'inRange' => [
                    3, 7
                ],
                'discount' => 2,
                'minDiscount' => 2,
                'maxAmount' => 15
            ],
            [
                'inRange' => [
                    7, 15
                ],
                'discount' => 3,
                'minDiscount' => 2,
                'maxAmount' => 15
            ],
            [
                'inRange' => [
                    15, 1000
                ],
                'discount' => 5,
                'minDiscount' => 2,
                'maxAmount' => 20
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

        # when retail price is less then 50$ - use for Wholesale price Retail price
        if($retailPrice >= 50 && $sellerCost !== $retailPrice) {

            foreach ($this->configuration as $settings) {
                if(null !== ($result = $this->recalculate($settings, $sellerCost, $retailPrice))) {
                    $newSellerPrice = $result;
                    break;
                }
            }
        }

        return $newSellerPrice;
    }

    /**
     * @param       $settings
     * @param float $sellerCost
     * @param float $retailPrice
     *
     * @return float|null
     */
    private function recalculate($settings, float $sellerCost, float $retailPrice): ?float
    {
        if($sellerCost > $retailPrice) {
            return null;
        }

        $range = $settings['inRange'];
        $onePercent = $sellerCost / 100;
        $priceDiff = $retailPrice - $sellerCost;
        $priceDiffPercents = $priceDiff / $onePercent;

        if($range[0] <= $priceDiffPercents && $priceDiffPercents < $range[1]) {

            # now we can calculate new Wholesale Price

            $margin = $this->getPercentOfNumber($retailPrice, $settings['discount']);
            $minMargin = $this->getPercentOfNumber($retailPrice, $settings['minDiscount']);
            $maxMargin = $settings['maxAmount'];

            # total margin should never be greater then $maxMargin;

            $result = $retailPrice - $margin;

            if($margin > $priceDiff) {

                $result = $minMargin >= $priceDiff
                    ? $retailPrice // if less then min margin
                    : $sellerCost + $minMargin;

                if ($result === $retailPrice) {
                    return $result;
                }

            } else {
                if($margin > $priceDiff/2) {
                    $result = $retailPrice - ($priceDiff/2);
                }

                if($margin < $priceDiff/2) {
                    $result = $retailPrice - ($margin >= $maxMargin ? $maxMargin : $margin);
                }
            }

            if($retailPrice - $result < $minMargin) {
                $result = $retailPrice - $minMargin;
            }

            # when more than max amount
            if($retailPrice - $result > $maxMargin) {
                $result = $retailPrice - $maxMargin;
            }

            # when more than retail price
            if($result > $retailPrice) {
                $result = $retailPrice;
            }

            return $result;
        }

        return null;
    }

    /**
     * @param float $number
     * @param float $percent
     *
     * @return float
     */
    private function getPercentOfNumber(float $number, float $percent): float
    {
        return ($number / 100) * $percent;
    }

}