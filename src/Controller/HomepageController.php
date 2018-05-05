<?php

namespace App\Controller;

use App\Processor\PriceStrategy\InRangeStrategy;

use App\Reader\AbstractReader;
use App\Reader\HotlineReader;
use App\Reader\RemainsReader;
use App\Schema;
use App\Writer\CsvWriter;

use App\Writer\FileWriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
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
     * @var FileWriter
     */
    private $fileWriter;

    /**
     * @var InRangeStrategy
     */
    private $inRangeStrategy;
//    /**
//     * @var FormFactoryInterface
//     */
//    private $formFactory;

    /**
     * HomepageController constructor.
     *
     * @param RemainsReader   $remainsReader
     * @param HotlineReader   $hotlineReader
     * @param InRangeStrategy $inRangeStrategy
     * @param FileWriter      $fileWriter
     */
    public function __construct(
        RemainsReader $remainsReader,
        HotlineReader $hotlineReader,
        InRangeStrategy $inRangeStrategy,
        FileWriter $fileWriter
    ) {
        $this->remainsReader = $remainsReader;
        $this->hotlineReader = $hotlineReader;

        $this->inRangeStrategy = $inRangeStrategy;
        $this->fileWriter = $fileWriter;
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
//        $form = $this->formFactory->create(RegistrationFormType::class, $data);

        echo 'yes';
        die;

    }


    /**
     * @Symfony\Component\Routing\Annotation\Route(
     *     name="analyze",
     *     path="/analyze"
     * )
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Method({"GET"})
     *
     * @return Response
     */
    public function analyzeAction()
    {
        // choose Remains document
        // choose column names for remains document
        $path = __DIR__ . '/../../public/downloads/1remains.csv';
        $remainsArray = $this->remainsReader->readFromFile($path);

//        echo '<pre>';
//        $list = array_column($remainsArray, 'sku');
//        sort($list);
//
//        print_R($list);
//        die;

        // choose Hotline document
        // choose column names for hotline document
        // choose conversion rate
        $path = __DIR__ . '/../../public/downloads/2hotline.csv';
        $hotlineArray = $this->hotlineReader->readFromFile($path);

        $result = [];

        foreach($remainsArray as $row) {
            $sku = $row[Schema::SKU];
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

            $wholesalePrice = $this->inRangeStrategy->process(
                $merged[Schema::SELLER_COST],
                $merged[Schema::RETAIL_PRICE]
            );

            $merged[Schema::WHOLESALE_PRICE] = round($wholesalePrice);
            $result[$sku] = $merged;
        }

        $path = __DIR__ . '/../../public/downloads/3result.xls';
        $this->fileWriter->path($path)->write($result);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment; filename="3result.xls"');
        $response->setContent(file_get_contents($path));
        return $response;
    }
}