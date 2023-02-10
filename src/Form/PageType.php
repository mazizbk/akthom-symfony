<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Entity\Page;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label'=>'Titre'])
            ->add('description', TextareaType::class, ['label'=>'Description','attr'=>['rows'=>'10']])
            ->add(
                'content',
                CKEditorType::class,
                [
                    'label'=> 'Contenu',
                    'attr' => ['class' => 'editor'],
                    'config' => [

                        'language' => 'fr',
                        'allowedContent'=>true,
                        'extraPlugins' => ['templates','content'],
                        'templates'    => 'my_template',

                    ],
                    'templates' => [
                        'my_template' => [
                            'templates'  => [
                                [
                                    'title'               => 'Accueil',
                                    'description'         => 'Style bootsrap accueil',
                                    'template'            => 'bloc/_part_1.html.twig',
                                ],

                                [
                                    'title'               => 'Pricing',
                                    'description'         => 'Style bootsrap prix',
                                    'template'            => 'bloc/_part_2.html.twig',
                                ],
                                [
                                    'title'               => 'Domaines',
                                    'description'         => 'Style bootsrap domaines',
                                    'template'            => 'bloc/_part_3.html.twig',
                                ],
                                [
                                    'title'               => 'Expertises',
                                    'description'         => 'Style bootsrap expertises',
                                    'template'            => 'bloc/_part_4.html.twig',
                                ],

                               
                                [
                                    'title'               => 'Le moteur de rercherche',
                                    'description'         => 'Style bootsrap le moteur de recherche',
                                    'template'            => 'bloc/_part_5.html.twig',
                                ]

                            ],
                        ],
                    ],
                ]
            )
            ->add('status', CheckboxType::class, ['label'=>'Publier le contenu','required'=>false])
            ->add('is_search_module', CheckboxType::class, ['label'=>'Inclure le module de recherche','required'=>false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
