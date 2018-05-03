<?php

namespace App\Reader;

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
     * @return array
     */
    protected function getSchema(): array
    {
        return [
//            self::NAME => 3,
            self::SKU => 10,
            self::RETAIL_PRICE => 5
        ];
    }

    /**
     * @return array
     */
    protected function getSchemaType(): array
    {
        return [
//            self::NAME => 'string',
            self::SKU => 'int',
            self::RETAIL_PRICE => 'float'
        ];
    }

    /**
     * @param string $name
     * @param mixed  $val
     *
     * @return mixed|string
     */
    protected function convertValue(string $name, $val)
    {
        if ($name === self::SKU) {
            $val = $this->skuFinder->getFromUrl($val);
        }

        $val = parent::convertValue($name, $val);

        if($name === self::RETAIL_PRICE) {
            $val = $this->currencyConverter->convert($val, 'UAH', true, 'USD');
        }

        return $val;
    }
}