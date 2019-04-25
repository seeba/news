<?php

namespace App\Form;

use App\Entity\News;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Tytuł',
                'required' => true

            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Aktywny',
                'required' => true
            ])
            ->add('publishedAt', DateType::class,
                ['label' => 'Data publikacji',
                 'widget' => 'single_text',
                 'html5' => false,
                 'required' => true
                ]);
        if ($options['edit_type'] == 'normal' ) {
            $builder->add('content', TextareaType::class,
                [
                    'label' => 'Treść',
                    'required' => true
                    ])
                    ->add('image', FileType::class, [
                        'data_class' => null,
                        'label' => 'Wybierz zdjęcie',
                        'required' => true])
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
            'edit_type' => 'normal'
        ]);
    }
}
