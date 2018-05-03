<?php

namespace App\Controller;

use App\Processor\PriceStrategy\InRangeStrategy;

use App\Reader\AbstractReader;
use App\Reader\HotlineReader;
use App\Reader\RemainsReader;
use App\Writer\CsvWriter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * Class HomepageController
 * @package App\Action
 */
class HomepageController extends Controller
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
     * @var CsvWriter
     */
    private $csvWriter;

    /**
     * @var InRangeStrategy
     */
    private $inRangeStrategy;

    /**
     * HomepageController constructor.
     *
     * @param RemainsReader   $remainsReader
     * @param HotlineReader   $hotlineReader
     * @param InRangeStrategy $inRangeStrategy
     * @param CsvWriter       $csvWriter
     */
    public function __construct(
        RemainsReader $remainsReader,
        HotlineReader $hotlineReader,
        InRangeStrategy $inRangeStrategy,
        CsvWriter $csvWriter
    ) {
        $this->remainsReader = $remainsReader;
        $this->hotlineReader = $hotlineReader;

        $this->inRangeStrategy = $inRangeStrategy;
        $this->csvWriter = $csvWriter;
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route(
     *     name="homepage",
     *     path="/"
     * )
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Method({"GET"})
     *
     * @return Response
     */
    public function indexAction()
    {
        // choose Remains document
        // choose column names for remains document
        $path = __DIR__ . '/../../public/downloads/1remains.csv';
        $remainsArray = $this->remainsReader->readFromFile($path);

        // choose Hotline document
        // choose column names for hotline document
        // choose conversion rate
        $path = __DIR__ . '/../../public/downloads/2hotline.csv';
        $hotlineArray = $this->hotlineReader->readFromFile($path);

        $result = [];

        foreach($remainsArray as $row) {
            $name = $row[AbstractReader::NAME]; // ToDO: change to SKU
            $merged = array_key_exists($name, $hotlineArray)
                ? array_merge(
                    [AbstractReader::SELLER_COST => 0, AbstractReader::RETAIL_PRICE => 0],
                    $row,
                    $hotlineArray[$name]
                )
                : array_merge(
                    [AbstractReader::SELLER_COST => 0, AbstractReader::RETAIL_PRICE => 0],
                    $row,
                    ['isNew' => 1]
                );

            $merged[AbstractReader::WHOLESALE_PRICE] = $this->inRangeStrategy->process(
                $merged[AbstractReader::SELLER_COST],
                $merged[AbstractReader::RETAIL_PRICE]
            );

            $result[$name] = $merged;
        }

        $path = __DIR__ . '/../../public/downloads/3result.xls';
        $this->csvWriter->path($path)->write($result);

        return $this->render('homepage.html.twig', []);
    }
}