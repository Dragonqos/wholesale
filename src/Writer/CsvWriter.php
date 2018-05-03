<?php

namespace App\Writer;

use App\Reader\AbstractReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Yectep\PhpSpreadsheetBundle\Factory;

/**
 * Class CsvWriter
 * @package App\Writer
 */
class CsvWriter implements WriterInterface
{
    /**
     * @var Factory
     */
    private $spreadSheetFactory;

    /**
     * @var
     */
    private $path;

    /**
     * @var array
     */
    private $schema = [
        'A' => AbstractReader::SKU,
        'B' => AbstractReader::NAME,
        'C' => AbstractReader::WHOLESALE_PRICE,
        'D' => AbstractReader::QUANTITY,
        'E' => AbstractReader::SELLER_COST,
        'F' => AbstractReader::RETAIL_PRICE,
    ];

    /**
     * AbstractReader constructor.
     *
     * @param Factory $spreadSheetFactory
     */
    public function __construct(Factory $spreadSheetFactory)
    {
        $this->spreadSheetFactory = $spreadSheetFactory;
    }

    /**
     * @param string $filePath
     *
     * @return WriterInterface
     */
    public function path(string $filePath): WriterInterface
    {
        $this->path = $filePath;
        return $this;
    }

    /**
     * @param array $items
     * @return void
     */
    public function write(array $items): void
    {
        /** @var Spreadsheet $spreadsheet */
        $spreadsheet = $this->spreadSheetFactory->createSpreadsheet();

        # Set Headers
        foreach ($this->schema as $colIndex => $name) {
            $spreadsheet->getActiveSheet()->getCell($colIndex.'1')->setValue($name);
            $spreadsheet->getActiveSheet()->getColumnDimension($colIndex)->setAutoSize(true)->setVisible(true);
        }

        $rowIndex = 2;

        # Set Body
        foreach ($items as $row) {
            foreach ($this->schema as $colIndex => $name) {

                $cell = $spreadsheet->getActiveSheet()
                    ->getCell($colIndex.$rowIndex)
                    ->setValue($row[$name] ?? '');

                # Apply Styles Body
                if($row['isNew'] ?? 0 === 1) {
                    $cell->getStyle()->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);
                }
            }

            ++$rowIndex;
        }

        /** @var Xls $writer */
        $writer = $this->spreadSheetFactory->createWriter($spreadsheet, 'Xls');
        $writer->setUseDiskCaching(true);
        $writer->save($this->path);
    }
}