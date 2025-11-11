<?php

namespace App\Form;

use App\Entity\Artista;
use App\Entity\PiezaDeArte;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PiezaDeArteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('anio')
            ->add('artista', EntityType::class, [
                'class' => Artista::class,
                'choice_label' => 'nombre',
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PiezaDeArte::class,
        ]);
    }
} // <-- El archivo DEBE terminar aquí.

//elegir artistas por id
    /*
    ->add('artista', EntityType::class, [
        'class' => Artista::class,
        'choice_label' => 'id',
    ])
    */
        
    //elegir artistas por nombre (más amigable para el usuario)
    /*
    ->add('artista', EntityType::class, [
        'class' => Artista::class,
        'choice_label' => 'nombre',
        'placeholder' => 'Selecciona un artista',
    ])
    */