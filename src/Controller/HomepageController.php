<?php

namespace App\Controller;

use App\Service\HotlineReader;
use App\Service\RemainsReader;
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

    private $hotlineReader;

    /**
     * HomepageController constructor.
     *
     * @param RemainsReader $remainsReader
     */
    public function __construct(
        RemainsReader $remainsReader,
        HotlineReader $hotlineReader
    ) {
        $this->remainsReader = $remainsReader;
        $this->hotlineReader = $hotlineReader;
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



        echo "<pre>";
        print_R($hotlineArray);
        die;


        // analyze

        // write file


        return $this->render('homepage.html.twig', []);
    }
}