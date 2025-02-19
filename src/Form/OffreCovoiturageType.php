<?php

namespace App\Form;

use App\Entity\OffreCovoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class OffreCovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('depart',null, [
                'required' => false,  // Champ non obligatoire
            ])
            ->add('destination',null, [
                'required' => false,  // Champ non obligatoire
            ])
            ->add('matVehicule',null, [
                'required' => false,  // Champ non obligatoire
            ])
            ->add('placesDispo',null, [
                'required' => false,  // Champ non obligatoire
            ])
            
            ->add('date', DateTimeType::class, [
                'required' => false,  // Champ non obligatoire
            ])
            ->add('prix',null, [
                'required' => false,  // Champ non obligatoire
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreCovoiturage::class,
        ]);
    }
}
