<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Page;
use App\Entity\Menu;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('description')
            ->add('parent_id',EntityType::class, [
                'label' => 'Menu Parent',
                'required'=>false,
                'placeholder' => '-- Choisir un menu parent --',
                'class' => Menu::class,
                'choice_label' => function (Menu $menu) {
                    return strtoupper($menu->getLabel());
                }
            ])
            ->add('page_id', EntityType::class, [
                'label' => 'Lien avec la page',
                'placeholder' => '-- Choisir une page --',
                'class' => Page::class,
                'choice_label' => function (Page $page) {
                    return strtoupper($page->getTitle());
                },
                'choice_value' => function (Page $page = null) {
                    return $page ? $page->getSlug() : '';
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
