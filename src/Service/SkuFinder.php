<?php

namespace App\Service;

/**
 * Class SkuFinder
 * @package App\Service
 */
class SkuFinder
{
    const LAST_CHUNK_INTEGER_REGEX = '/(?:.*-)([0-9]+)/';

    /**
     * @param string $string
     *
     * @return int|null
     */
    public function getFromUrl(string $string): ?int
    {
        preg_match(self::LAST_CHUNK_INTEGER_REGEX, $string, $matches);
        return $matches[1] ?? null;
    }
}