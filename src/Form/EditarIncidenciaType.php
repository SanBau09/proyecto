<?php

namespace App\Form;

use App\Entity\Incidencia;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EditarIncidenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('estado', ChoiceType::class, [
                'choices' => [
                    'Iniciada' => 'iniciada',
                    'En proceso' => 'en_proceso',
                    'Resuelta' => 'resuelta',
                ],
            ])
            ->add('Editar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Incidencia::class,
        ]);
    }
}