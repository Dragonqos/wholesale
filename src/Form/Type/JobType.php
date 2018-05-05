<?php

namespace App\Form\Type;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class JobType
 * @package App\Form\Type
 */
class JobType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('warehousePrice', FileType::class, [
                'required' => true,
                'help' => 'Choose CSV file exported from 1C.',
            ])
            ->add('hotlinePrice', FileType::class, [
                'required' => true,
                'help' => 'Choose CSV file exported from Hotline.',
            ])
            ->add('rate', TextType::class, [
                'required' => true,
                'label' => 'Currency rate',
                'help' => 'Today\'s currency rate UAH => USD',
                'attr' => [
                    'class' => 'mb-2',
                ]
            ])
            ->add('Get wholesale price', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-outline-success'
                ]
            ]);

        $builder
            ->get('warehousePrice')
            ->addModelTransformer(new CallbackTransformer(
                function ($fileString) {
                    return $fileString ? new File($fileString, false) : null;
                },
                function ($uploadedFile) {
                    return $uploadedFile;
                }
            ));

        $builder
            ->get('hotlinePrice')
            ->addModelTransformer(new CallbackTransformer(
                function ($fileString) {
                    return $fileString ? new File($fileString, false) : null;
                },
                function ($uploadedFile) {
                    return $uploadedFile;
                }
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}