<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\Type\JobType;
use App\Processor\Processor;
use App\Repository\JobRepository;
use App\Service\FileUploader;
use Lexik\Bundle\CurrencyBundle\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomepageController
 * @package App\Action
 */
class HomepageController extends Controller
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * HomepageController constructor.
     *
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @Route("/", name="homepage", methods="GET|POST")
     * @param Request      $request
     * @param FileUploader $fileUploader
     *
     * @return Response
     */
    public function indexAction(Request $request, FileUploader $fileUploader): Response
    {
        $job = new Job();
        $this->setLastCurrency($job);

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $warehousePrice = $job->getWarehousePrice();

            if ($warehousePrice) {
                $fileName = $fileUploader->upload($warehousePrice);
                $job->setWarehousePrice($fileName);
            }

            $hotlinePrice = $job->getHotlinePrice();

            if ($hotlinePrice) {
                $fileName = $fileUploader->upload($hotlinePrice);
                $job->setHotlinePrice($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            $this->executeJob($job);

            $filePath = $this->getParameter('app.download.dir') . '/'. $job->getWholesalePrice();
            return $this->file($filePath);
        }

        return $this->render('homepage.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Job $job
     */
    private function executeJob(Job $job): void
    {
        $this->setLastCurrency($job);
        $this->processor->process($job);

        if($job->hasWholesalePrice()) {
            // clean old results with files
            $em = $this->getDoctrine()->getManager();

            /** @var JobRepository $repo */
            $repo = $em->getRepository(Job::class);
            $repo->removeOldRecords();
            $em->flush();
        }
    }

    /**
     * @param Job $job
     */
    private function setLastCurrency(Job $job): void
    {
        $repo = $this->getDoctrine()->getRepository(Currency::class);
        $currency = $repo->findOneBy([
            'code' => 'USD',
        ]);

        if($currency instanceof Currency) {
            $jobRate = $job->getRate();
            if(null !== $jobRate) {
                $currency->setRate($jobRate);
            }

            $currencyRate = $currency->getRate();
            if(null !== $currencyRate) {
                $job->setRate($currencyRate);
            }
        }
    }
}