<?php

namespace App\Reader;

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
            self::SELLER_COST => 'float',
            self::QUANTITY => 'int'
        ];
    }
}