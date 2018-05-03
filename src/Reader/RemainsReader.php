<?php

namespace App\Reader;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Class RemainsReader
 * @package App\Reader
 */
class RemainsReader extends AbstractReader
{
    /**
     * @return array
     */
    protected function getSchema(): array
    {
        return [
            self::NAME => 1,
            self::SKU => 1,
            self::SELLER_COST => 4,
            self::QUANTITY => 2
        ];
    }

    /**
     * @return array
     */
    protected function getSchemaType(): array
    {
        return [
            self::NAME => 'string',
            self::SKU => 'string',
            self::SELLER_COST => 'float',
            self::QUANTITY => 'int'
        ];
    }

    /**
     * @param Worksheet $worksheet
     * @param int       $rowIndex
     *
     * @return array
     */
    protected function readLine(Worksheet $worksheet, int &$rowIndex): array
    {
        // reading first line
        $res = parent::readLine($worksheet, $rowIndex);
        $res[self::SKU] = null;

        if(array_key_exists(self::SELLER_COST, $res) && array_key_exists(self::QUANTITY, $res)) {
            // every next line with the same key

            $token = serialize([$res[self::SELLER_COST], $res[self::QUANTITY]]);
            while(true) {
                $rowIndex++;
                $nextLine = parent::readLine($worksheet, $rowIndex);

                if(!array_key_exists(self::SELLER_COST, $nextLine) || !array_key_exists(self::QUANTITY, $nextLine)) {
                    return $res;
                }

                $newLineToken = serialize([$nextLine[self::SELLER_COST], $nextLine[self::QUANTITY]]);

                if($token !== $newLineToken) {
                    --$rowIndex;
                    $res[self::SKU] = null;
                    return $res;
                }

                $sku = $nextLine[self::SKU] ?? null;
                if(null !== $sku) {
                    $sku = preg_replace('/[\W]+/i', '', $sku);
                }

                if(is_numeric($sku)) {
                    $res[self::SKU] = $sku;
                    return $res;
                }
            }
        }

        return $res;
    }

}