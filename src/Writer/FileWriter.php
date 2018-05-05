<?php

namespace App\Writer;

use App\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yectep\PhpSpreadsheetBundle\Factory;

/**
 * Class CsvWriter
 * @package App\Writer
 */
class FileWriter implements WriterInterface
{
    /**
     * @var Factory
     */
    private $spreadSheetFactory;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $ext;

    /**
     * @var array
     */
    private $schema;

    /**
     * FileWriter constructor.
     *
     * @param Factory $spreadSheetFactory
     * @param array   $schema
     */
    public function __construct(Factory $spreadSheetFactory, array $schema)
    {
        $this->spreadSheetFactory = $spreadSheetFactory;
        $this->schema = $schema;
    }

    /**
     * @param string $filePath
     *
     * @return WriterInterface
     */
    public function path(string $filePath): WriterInterface
    {
        $this->path = $filePath;

        $parts = explode('.', $filePath);
        $this->ext = ucfirst(end($parts));

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

        /** @var Xls|Xlsx $writer */
        $writer = $this->spreadSheetFactory->createWriter($spreadsheet, $this->ext);
        $writer->setUseDiskCaching(true);
        $writer->save($this->path);
    }
}