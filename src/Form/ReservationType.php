<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Enum\StatutReservation;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
        ->add('statut', ChoiceType::class, [
            'choices' => array_combine(
                array_map(fn($statut) => $statut->name, StatutReservation::cases()), // Labels
                StatutReservation::cases() // Valeurs
            ),
            'choice_label' => fn($choice) => $choice->name, // Affiche le nom de l'énumération
            'expanded' => false, // True pour afficher des boutons radio
            'multiple' => false, // True pour une sélection multiple
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
