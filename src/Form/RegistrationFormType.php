<?php
// src/Form/RegistrationFormType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'mapped' => false, // On ne veut pas écraser le hash
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('first_name', TextType::class, [
                'label' => 'First Name',
                'attr' => ['class' => 'form-control'],
                'required' => false, // Facultatif
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Last Name',
                'attr' => ['class' => 'form-control'],
                'required' => false, // Facultatif
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Telephone',
                'attr' => ['class' => 'form-control'],
                'required' => false, // Facultatif
            ])
            ->add('vehicule', TextType::class, [
                'label' => 'Vehicule',
                'attr' => ['class' => 'form-control'],
                'required' => false, // Facultatif
            ])
            ->add('image', TextType::class, [
                'label' => 'Image URL',
                'attr' => ['class' => 'form-control'],
                'required' => false, // Facultatif
            ])
           
            ->add('auth_method', ChoiceType::class, [
                'label' => 'Auth Method',
                'choices' => [
                    'Email' => 'email',
                    'Phone' => 'phone',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I agree to the terms',
                'attr' => ['class' => 'form-check-input'],
                'mapped' => false, // Ce champ n'est pas mappé à l'entité User
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Lie le formulaire à l'entité User
        ]);
    }
}