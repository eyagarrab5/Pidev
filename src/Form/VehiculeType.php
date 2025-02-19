<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_vehicule', TextType::class, [
                'label' => 'Type de véhicule',
               
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                
            ])
            ->add('role', TextType::class, [
                'label' => 'Rôle',
                
            ])
            ->add('prix_par_heure', NumberType::class, [
                'label' => 'Prix par heure',
                
            ])
            ->add('prix_par_jour', NumberType::class, [
                'label' => 'Prix par jour',
                
            ])
            ->add('disponibilite', TextType::class, [
                'label' => 'Disponibilité',
               
            ])
            ->add('lieu_retrait', TextType::class, [
                'label' => 'Lieu de retrait',
                
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du véhicule',
                'mapped' => false,
                
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, WEBP).',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}