<?php

namespace App\Processor\PriceStrategy;

interface PriceStrategyInterface
{
    /**
     * @param float $sellerCost
     * @param float $retailPrice
     *
     * @return float
     */
    public function process(float $sellerCost, float $retailPrice): float;
}