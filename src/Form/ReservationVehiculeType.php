<?php

namespace App\Form;

use App\Entity\ReservationVehicule;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ReservationVehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $vehiculeId = $options['vehicule_id'] ?? null; // Get vehicle ID from form options

        $builder
            ->add('date_debut', null, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text',
            ])
            ->add('prix_total')
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'confirmée' => 'confirmée',
                ],
                'placeholder' => 'Choisissez un statut',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            
           ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationVehicule::class,
            'vehicule_id' => null, // Define the option for vehicle ID
        ]);
    }
}
