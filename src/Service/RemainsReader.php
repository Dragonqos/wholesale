<?php

namespace App\Service;

/**
 * Class RemainsReader
 * @package App\Service
 */
class RemainsReader extends AbstractReader
{
    /**
     * @return array
     */
    protected function getSchema(): array
    {
        return [
            'name' => 1,
            'seller_cost' => 4,
            'quantity' => 2
        ];
    }

    /**
     * @return array
     */
    protected function getSchemaType(): array
    {
        return [
            'name' => 'string',
            'seller_cost' => 'float',
            'quantity' => 'int'
        ];
    }
}