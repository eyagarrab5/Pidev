<?php

namespace App\Form;

use App\Entity\OffreCovoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File; // Import the correct File constraint

class OffreCovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('depart', null, [
                'required' => false,  
            ])
            ->add('destination', null, [
                'required' => false,  
            ])
            ->add('matVehicule', null, [
                'required' => false,  
            ])
            ->add('placesDispo', null, [
                'required' => false,  
            ])
            ->add('date', DateTimeType::class, [
                'required' => false,  
            ])
            ->add('prix', null, [
                'required' => false,  
            ])
            ->add('brochure', FileType::class, [
                'label' => "Image du véhicule",
                'mapped' => false, // This ensures the field is not mapped to the entity
                'required' => false,
                'constraints' => [
                    new File([ // Use the correct File constraint here
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Formats autorisés : JPEG, PNG, WEBP',
                        'maxSizeMessage' => 'La taille maximale autorisée est {{ limit }}'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ]
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