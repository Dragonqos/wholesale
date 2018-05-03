<?php

namespace App\Processor;

use App\Processor\PriceStrategy\PriceStrategyInterface;

/**
 * Class Processor
 * @package App\Processor
 */
class Processor {

    /**
     * @var PriceStrategyInterface
     */
    private $priceStrategy;

    /**
     * Processor constructor.
     *
     * @param PriceStrategyInterface $priceStrategy
     */
    public function __construct(PriceStrategyInterface $priceStrategy)
    {
        $this->priceStrategy = $priceStrategy;
    }

}