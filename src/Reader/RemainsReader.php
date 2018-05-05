<?php

namespace App\Reader;

use App\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Class RemainsReader
 * @package App\Reader
 */
class RemainsReader extends AbstractReader
{
    /**
     * @param Worksheet $worksheet
     * @param int       $rowIndex
     * @param int       $index
     *
     * @return array
     */
    protected function readLine(Worksheet $worksheet, int &$rowIndex, int $index): array
    {
        // reading first line
        $res = parent::readLine($worksheet, $rowIndex, $index);
        $res[Schema::SKU] = 'sku--' . $rowIndex;

        if(array_key_exists(Schema::SELLER_COST, $res) && array_key_exists(Schema::QUANTITY, $res)) {
            // every next line with the same key

            $token = serialize([$res[Schema::SELLER_COST], $res[Schema::QUANTITY], $index]);
            while(true) {
                $rowIndex++;
                $nextLine = parent::readLine($worksheet, $rowIndex, $index);

                $cost = $nextLine[Schema::SELLER_COST] ?? null;
                $qty = $nextLine[Schema::QUANTITY] ?? null;

                $newLineToken = serialize([$cost, $qty, $index]);

                if($token !== $newLineToken) {
                    $res[Schema::SKU] = 'sku--' . $rowIndex;
                    --$rowIndex;
                    return $res;
                }

                $sku = $nextLine[Schema::SKU] ?? null;
                if(null !== $sku) {
                    $sku = preg_replace('/[\W]+/i', '', $sku);
                }

                if(is_numeric($sku)) {
                    $res[Schema::SKU] = $sku;
                    return $res;
                }
            }
        }

        return $res;
    }
}