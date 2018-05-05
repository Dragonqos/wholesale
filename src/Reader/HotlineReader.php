<?php

namespace App\Reader;

use App\Schema;
use App\Service\SkuFinder;
use Lexik\Bundle\CurrencyBundle\Currency\Converter;
use Yectep\PhpSpreadsheetBundle\Factory;

/**
 * Class HotlineReader
 * @package App\Reader
 */
class HotlineReader extends AbstractReader
{
    /**
     * @var Converter
     */
    private $currencyConverter;

    /**
     * HotlineReader constructor.
     *
     * @param Converter $currencyConverter
     * @param Factory                                         $spreadSheetFactory
     * @param SkuFinder                                       $skuFinder
     */
    public function __construct(Converter $currencyConverter, Factory $spreadSheetFactory, SkuFinder $skuFinder)
    {
        $this->currencyConverter = $currencyConverter;
        parent::__construct($spreadSheetFactory, $skuFinder);
    }

    /**
     * @param string $name
     * @param mixed  $val
     *
     * @return mixed|string
     */
    protected function convertValue(string $name, $val)
    {
        if ($name === Schema::SKU) {
            $val = $this->skuFinder->getFromUrl($val);
        }

        $val = parent::convertValue($name, $val);

        if($name === Schema::RETAIL_PRICE) {
            $val = $this->currencyConverter->convert($val, 'UAH', true, 'USD');
        }

        return $val;
    }
}