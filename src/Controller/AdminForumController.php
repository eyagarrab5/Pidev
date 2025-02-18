<?php

namespace App\Controller;

use App\Entity\ForumPosts;
use App\Entity\Comments;
use App\Repository\ForumPostsRepository;
use App\Form\CommentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/forum')]
class AdminForumController extends AbstractController
{
    #[Route('/posts', name: 'app_admin_forum_posts')]
    public function posts(ForumPostsRepository $forumPostsRepository): Response
    {
        // Récupérer tous les posts avec leurs commentaires
        $forumPosts = $forumPostsRepository->findAllWithComments();

        return $this->render('admin/posts.html.twig', [
            'forum_posts' => $forumPosts,
        ]);
    }

    #[Route('/posts/{id}/delete', name: 'app_admin_forum_post_delete', methods: ['POST'])]
    public function deletePost(Request $request, ForumPosts $forumPost, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete'.$forumPost->getId(), $request->request->get('_token'))) {
            // Supprimer le post
            $entityManager->remove($forumPost);
            $entityManager->flush();

            $this->addFlash('success', 'Le post a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_forum_posts');
    }

    #[Route('/comments/{id}/delete', name: 'app_admin_forum_comment_delete', methods: ['POST'])]
    public function deleteComment(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            // Supprimer le commentaire
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_forum_posts');
    }

    #[Route('/posts/{id}/comment', name: 'app_admin_forum_post_comment', methods: ['GET', 'POST'])]
    public function commentPost(Request $request, ForumPosts $forumPost, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de l'entité Comments
        $comment = new Comments();
    
        // Créer le formulaire pour ajouter un commentaire
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
    
        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le commentaire au post de forum
            $comment->setForumPost($forumPost);
    
            // Définir la date de création du commentaire
            $comment->setCreatedAt(new \DateTime());
    
            // Enregistrer le commentaire en base de données
            $entityManager->persist($comment);
            $entityManager->flush();
    
            // Ajouter un message flash pour indiquer que le commentaire a été ajouté avec succès
            $this->addFlash('success', 'Le commentaire a été ajouté avec succès.');
    
            // Rediriger vers la liste des posts de forum
            return $this->redirectToRoute('app_admin_forum_posts');
        }
    
        // Afficher le formulaire dans le template Twig
        return $this->render('admin/comment_form.html.twig', [
            'form' => $form->createView(),
            'forum_post' => $forumPost,
        ]);
    }
}