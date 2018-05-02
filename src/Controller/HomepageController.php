<?php

namespace App\Controller;

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
        return $this->render('homepage.html.twig', []);
    }
}