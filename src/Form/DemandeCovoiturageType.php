<?php
namespace App\Form;

use App\Entity\DemandeCovoiturage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class DemandeCovoiturageType extends AbstractType
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
            ->add('date', DateTimeType::class, [
                'required' => false,  
            ])
            ->add('budget', null, [
                'required' => false, 
            ])
        ;
    }

  
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeCovoiturage::class,
        ]);
    }
}
