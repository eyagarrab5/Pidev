<?php

namespace App\Form;

use App\Entity\Comments;
use App\Entity\ForumPosts; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('content', TextareaType::class, [
            'label' => 'Contenu du commentaire',
            'attr' => ['rows' => 5],
        ]) // Ajout d'un type texte pour 'content'
        ->add('tags')
        ->add('attachments')
        ->add('forumPost', EntityType::class, [
            'class' => ForumPosts::class,   // La classe de l'entité ForumPost
            'choice_label' => 'title',     // Le champ du forum post à afficher dans la liste
            'placeholder' => 'Choisir un post', // Optionnel: pour afficher un texte de placeholder
            'label' => 'Post de Forum'    // Libellé du champ
        ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
