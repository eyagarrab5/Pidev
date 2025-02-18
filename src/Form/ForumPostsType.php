<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\ForumPosts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ForumPostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            /*->add('userFirstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('userLastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])*/
            ->add('category')
            ->add('tags')
            ->add('attachment', FileType::class, [
                'label' => 'Pièces jointes (PDF, JPG, PNG)',
                'mapped' => false, // Cela signifie que ce champ ne sera pas lié directement à l'entité
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M', // Limite de taille
                        'mimeTypes' => ['application/pdf', 'image/jpeg', 'image/png'], // Types de fichiers autorisés
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF, JPG ou PNG valide.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumPosts::class,
        ]);
    }
}
