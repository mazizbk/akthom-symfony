<?php

namespace App\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;

class MetaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        if ($options['data']['metadata']) {
            foreach ($options['data']['metadata'] as $key => $value) {

                $field_type = TextType::class;
                $disabled = false;
                if (is_array($value)) {
                    $builder->add($key, CollectionType::class, [
                        'entry_type' => TextType::class,
                        'allow_add' => false,
                        'prototype' => false,
                        'by_reference' => \false,
                        'data' => $value,
                        'disabled' => $disabled
                    ]);
                } else {
                    if (strtotime($value) !== false) {
                        $disabled = true;
                    }
                    $builder->add($key, $field_type, [
                        'data' => $value,
                        'disabled' => $disabled
                    ]);
                }
            }
            $builder->add('submit', SubmitType::class, ['label' => 'Valider'])
                ->getForm();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
