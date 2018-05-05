<?php

namespace App\Reader;

use App\Schema;
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
     * @var array
     */
    protected $schema = [];

    /**
     * @var array
     */
    protected $schemaType = [];

    /**
     * @param array $schema
     */
    public function setSchema(array $schema)
    {
        foreach($schema as $item) {
            $this->schema[$item['name']] = $item['fieldIndex'];
            $this->schemaType[$item['name']] = $item['fieldType'];
        }
    }

    /**
     * @return array
     */
    protected function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * @return array
     */
    protected function getSchemaType(): array
    {
        return $this->schemaType;
    }

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

        $rowIndex = 1;
        $totalRows = iterator_count($worksheet->getRowIterator());

        $describedRows = 0;

        /**
         * @var Row $row
         */
        while ($rowIndex <= $totalRows) {
            $res = $this->readLine($worksheet, $rowIndex, $describedRows);
            if (!empty($res) && isset($res[Schema::SKU])) {

                $sku = $res[Schema::SKU];

                if(array_key_exists($sku, $result)) {
                    $sku .= '-' . $rowIndex;
                }

                $result[$sku] = $res;
                ++$describedRows;
            }

            ++$rowIndex;
        }

        return $result;
    }

    /**
     * @param Worksheet $worksheet
     * @param int       $rowIndex
     *
     * @return array
     */
    protected function readLine(Worksheet $worksheet, int &$rowIndex, int $index): array
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
     * @param mixed  $val
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