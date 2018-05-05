<?php

namespace App\Processor;

use App\Entity\Job;
use App\Processor\PriceStrategy\InRangeStrategy;
use App\Reader\HotlineReader;
use App\Reader\RemainsReader;
use App\Schema;
use App\Writer\FileWriter;

/**
 * Class Processor
 * @package App\Processor
 */
class Processor
{
    /**
     * @var RemainsReader
     */
    private $remainsReader;

    /**
     * @var HotlineReader
     */
    private $hotlineReader;

    /**
     * @var FileWriter
     */
    private $fileWriter;

    /**
     * @var InRangeStrategy
     */
    private $inRangeStrategy;

    /**
     * @var string
     */
    private $uploadPath;

    /**
     * Processor constructor.
     *
     * @param RemainsReader   $remainsReader
     * @param HotlineReader   $hotlineReader
     * @param InRangeStrategy $inRangeStrategy
     * @param FileWriter      $fileWriter
     * @param string          $uploadPath
     * @param string          $downloadPath
     */
    public function __construct(
        RemainsReader $remainsReader,
        HotlineReader $hotlineReader,
        InRangeStrategy $inRangeStrategy,
        FileWriter $fileWriter,
        string $uploadPath,
        string $downloadPath
    )
    {
        $this->remainsReader = $remainsReader;
        $this->hotlineReader = $hotlineReader;

        $this->inRangeStrategy = $inRangeStrategy;
        $this->fileWriter = $fileWriter;
        $this->uploadPath = $uploadPath;
        $this->downloadPath = $downloadPath;
    }

    /**
     * @param Job $job
     */
    public function process(Job $job)
    {
        $remainsArray = $this->getRemains($job);
        $hotlineArray = $this->getHotline($job);

        $wholesaleArray = [];

        foreach ($remainsArray as $row) {
            $sku    = $row[Schema::SKU];
            $merged = array_key_exists($sku, $hotlineArray)
                ? array_merge(
                    [Schema::SELLER_COST => 0, Schema::RETAIL_PRICE => 0],
                    $row,
                    $hotlineArray[$sku]
                )
                : array_merge(
                    [Schema::SELLER_COST => 0, Schema::RETAIL_PRICE => 0],
                    $row,
                    ['isNew' => 1]
                );

            $wholesaleArray[$sku] = $this->applyStrategy($merged);
        }

        $this->write($job, $wholesaleArray);
    }

    /**
     * @param Job $job
     *
     * @return array
     */
    protected function getRemains(Job $job): array
    {
        $filePath = rtrim($this->uploadPath, '/');
        $fileName = ltrim($job->getWarehousePrice(), '/');

        $path = sprintf('%s/%s', $filePath, $fileName);
        return $this->remainsReader->readFromFile($path);
    }

    /**
     * @param Job $job
     *
     * @return array
     */
    protected function getHotline(Job $job): array
    {
        $filePath = rtrim($this->uploadPath, '/');
        $fileName = ltrim($job->getHotlinePrice(), '/');

        $path = sprintf('%s/%s', $filePath, $fileName);
        return $this->hotlineReader->readFromFile($path);
    }

    /**
     * ToDO: make ability to change strategies from StrategyRegistry and Job strategy type
     * @param $row
     *
     * @return array
     */
    protected function applyStrategy(array $row): array
    {
        $wholesalePrice = $this->inRangeStrategy->process(
            $row[Schema::SELLER_COST],
            $row[Schema::RETAIL_PRICE]
        );

        $row[Schema::WHOLESALE_PRICE] = round($wholesalePrice);

        return $row;
    }

    /**
     * @param Job   $job
     * @param array $rows
     */
    protected function write(Job $job, array $rows) {

        $filePath = rtrim($this->downloadPath, '/');
        $fileName = sprintf('%s_v%s.%s', (new \DateTime())->format('Y-m-d'), $job->getId(), 'xls');

        $path = sprintf('%s/%s', $filePath, $fileName);

        $this->fileWriter
            ->path($path)
            ->write($rows);

        $job->setWholesalePrice($fileName);
    }
}