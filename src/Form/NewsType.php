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
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsType extends AbstractType
{

    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class, [
                'label' => 'Tytuł',
                'required' => true

            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Aktywny',

            ])
            ->add('publishedAt', DateType::class,
                ['label' => 'Data publikacji',
                 'widget' => 'single_text',
                 'html5' => false,
                 'required' => true
                ]);
        if ($options['edit_type'] == 'normal' or $options['edit_type'] == 'new') {
            $builder->add('content', TextareaType::class,
                [

                    'label' => 'Treść',
                    'required' => true
                    ]);
        }
        if ($options['edit_type'] == 'new'){

            $builder->add('image', FileType::class, [
                'label' => 'Wybierz zdjęcie',
                'required' => true,
                'data_class' => null,

            ]);
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
