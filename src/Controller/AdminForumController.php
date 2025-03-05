<?php

namespace App\Controller;

use App\Entity\ForumPosts;
use App\Entity\Comments;
use App\Repository\UserRepository;
use App\Repository\ForumPostsRepository;
use App\Form\CommentsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/forum')]
class AdminForumController extends AbstractController
{
    #[Route('/posts', name: 'app_admin_forum_posts')]
    public function posts(Request $request, ForumPostsRepository $forumPostsRepository, PaginatorInterface $paginator): Response
    {
        $sort = $request->query->get('sort', 'newest');
        $search = $request->query->get('search'); 

        // Récupérer tous les posts avec leurs commentaires
        $forumPosts = $forumPostsRepository->findBySearchAndSort($search, $sort);

        // Récupérer tous les posts avec leurs commentaires
        $forumPosts = $forumPostsRepository->findAllWithComments();
        $query = $forumPostsRepository->findAllWithCommentsQuery($search, $sort);

         // Paginer les résultats
         $forumPosts = $paginator->paginate(
            $query, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page
            5 // Nombre d'éléments par page
        );


        // Récupérer les statistiques
        $totalPosts = $forumPostsRepository->getTotalPosts();
        $totalComments = $forumPostsRepository->getTotalComments();
        $mostLikedPost = $forumPostsRepository->getMostLikedPost();
        $mostCommentedPost = $forumPostsRepository->getMostCommentedPost();
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/posts.html.twig', [
                'forum_posts' => $forumPosts,
                'ajax_request' => true, // Ajouter un indicateur pour les requêtes AJAX
            ]);
        }
        $ajaxRequest = $request->isXmlHttpRequest();
        return $this->render('admin/posts.html.twig', [
            'forum_posts' => $forumPosts,
            'total_posts' => $totalPosts,
            'total_comments' => $totalComments,
            'most_liked_post' => $mostLikedPost,
            'most_commented_post' => $mostCommentedPost,
            'ajax_request' => $ajaxRequest,
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
            // Récupérer le post associé au commentaire
            $forumPost = $comment->getForumPost();
            // Décrémenter le compteur de commentaires du post
            $forumPost->setCommentsCount($forumPost->getCommentsCount() - 1);
            $entityManager->persist($forumPost); // Mettre à jour le post
            $entityManager->flush();
            // Supprimer le commentaire
            $entityManager->remove($comment);
            $entityManager->persist($forumPost); // Mettre à jour le post
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_forum_posts');
    }

    #[Route('/posts/{id}/comment', name: 'app_admin_forum_post_comment', methods: ['GET', 'POST'])]
    public function commentPost(Request $request, ForumPosts $forumPost, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'utilisateur "Administration"
            $adminUser = $userRepository->findOneBy(['first_name' => 'Administration']);

            if (!$adminUser) {
                throw $this->createNotFoundException('Utilisateur "Administration" non trouvé.');
            }

            // Associer le commentaire au post et à l'utilisateur "Administration"
            $comment->setForumPost($forumPost);
            $comment->setUser($adminUser); // Associer l'utilisateur "Administration"
            $comment->setCreatedAt(new \DateTime());
            $comment->setUpdatedAt(new \DateTime());

            // Incrémenter le compteur de commentaires du post
            $forumPost->setCommentsCount($forumPost->getCommentsCount() + 1);
            $entityManager->persist($forumPost); // Mettre à jour le post
            $entityManager->flush();
            // Initialiser attachments si nécessaire
            if ($comment->getAttachments() === null) {
            $comment->setAttachments(''); // Ou une valeur par défaut
             }

            $entityManager->persist($comment);
            $entityManager->persist($forumPost); // Mettre à jour le post
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été ajouté avec succès.');
            return $this->redirectToRoute('app_admin_forum_posts');
        }

        return $this->render('admin/comment_form.html.twig', [
            'form' => $form->createView(),
            'forum_post' => $forumPost,
        ]);
    }
}