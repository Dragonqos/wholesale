<?php

namespace App\Writer;

/**
 * Interface WriterInterface
 * @package App\Writer
 */
interface WriterInterface
{
    /**
     * @param string $path
     *
     * @return WriterInterface
     */
    public function path(string $path): WriterInterface;

    /**
     * @param array $items
     *
     * @return mixed
     */
    public function write(array $items);
}