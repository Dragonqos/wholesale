<?php

namespace App\Reader;

use App\Service\SkuFinder;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yectep\PhpSpreadsheetBundle\Factory;

/**
 * Class AbstractReader
 * @package App\Reader
 */
abstract class AbstractReader
{
    const SKU = 'sku';
    const NAME = 'name';
    const RETAIL_PRICE = 'retail_price';
    const SELLER_COST = 'seller_cost';
    const WHOLESALE_PRICE = 'wholesale_price';
    const QUANTITY = 'quantity';

    private $floatRegex = '/[^0-9\.\-]/';

    /**
     * @var Factory
     */
    private $spreadSheetFactory;

    /**
     * @var SkuFinder
     */
    protected $skuFinder;

    /**
     * AbstractReader constructor.
     *
     * @param Factory   $spreadSheetFactory
     * @param SkuFinder $skuFinder
     */
    public function __construct(Factory $spreadSheetFactory, SkuFinder $skuFinder)
    {
        $this->spreadSheetFactory = $spreadSheetFactory;
        $this->skuFinder = $skuFinder;
    }

    /**
     * @return array
     */
    abstract protected function getSchema(): array;

    /**
     * @return array
     */
    abstract protected function getSchemaType(): array;

    /**
     * @param string $filePath
     *
     * @return array
     */
    public function readFromFile(string $filePath): array
    {
        /** @var Csv $spreadsheet */
        $reader = $this->spreadSheetFactory->createReader('Csv');

        /** @var Spreadsheet $spreadsheet */
        $spreadsheet = $reader->load($filePath);

        /** @var Worksheet $worksheet */
        $worksheet = $spreadsheet->getActiveSheet();

        $result = [];

        /**
         * @var Row $row
         */
        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $res = $this->readLine($worksheet, $rowIndex);
            if (!empty($res)) {
                $result[$res[self::NAME]] = $res;
            }
        }

        return $result;
    }

    /**
     * @param Worksheet $worksheet
     * @param int       $rowIndex
     *
     * @return array
     */
    protected function readLine(Worksheet $worksheet, int $rowIndex): array
    {
        $res = [];
        foreach ($this->getSchema() as $name => $nameIndex) {
            $col = $worksheet->getCellByColumnAndRow($nameIndex, $rowIndex, false);
            if (null !== $col) {
                $res[$name] = $this->convertValue($name, $col->getFormattedValue());
            }
        }

        return $res;
    }

    /**
     * @param string $name
     * @param mixed $val
     *
     * @return mixed|string
     */
    protected function convertValue(string $name, $val)
    {
        $schemaType = $this->getSchemaType();
        $type = $schemaType[$name];

        if (in_array($type, ['int', 'float'], true)) {
            $val = str_replace(',', '.', $val);
            $val = preg_replace($this->floatRegex, '', $val);
            settype($val, $schemaType[$name]);
        }

        return $val;
    }
}