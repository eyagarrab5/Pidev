<?php

namespace App\Controller;

use App\Entity\ForumPosts;
use App\Entity\User; 
use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\ForumPostsRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comments')]
final class CommentsController extends AbstractController{
    #[Route('/', name: 'app_comments_index')]
    public function index(ForumPostsRepository $forumPostsRepository, CommentsRepository $commentsRepository): Response
    {
        return $this->render('comments/index.html.twig', [
            'comments' => $commentsRepository->findAll(),
            'forumPosts' => $forumPostsRepository->findAll(),
        ]);
    }


    #[Route('/new/{postId}', name: 'app_comments_new', methods: ['GET', 'POST'])]
    public function new(int $postId, Request $request, EntityManagerInterface $entityManager, ForumPostsRepository $forumPostsRepository): Response
    {
    $forumPost = $forumPostsRepository->find($postId);

    if (!$forumPost) {
        throw $this->createNotFoundException('Post non trouvé');
    }

    // Créer un nouveau commentaire
    $comment = new Comments();
    $comment->setForumPost($forumPost); // Associer le commentaire au post
    // Créer le formulaire
    $form = $this->createForm(CommentsType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $entityManager->getRepository(User::class)->find(1); // Remplacez 1 par l'ID de l'utilisateur que vous souhaitez simuler
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        $comment->setUser($user); // Associer l'utilisateur au commentaire
        $comment->setForumPost($forumPost);
        // Définir les dates de création et de mise à jour
        $comment->setCreatedAt(new \DateTime());
        $comment->setUpdatedAt(new \DateTime());


            // Gérer l'upload de la pièce jointe
            $file = $request->files->get('attachments');
            if ($file) {
                // Traitement de l'upload : génération d'un nom unique et déplacement du fichier
                $newFilename = uniqid().'.'.$file->guessExtension();
                // Assurez-vous que le paramètre "uploads_directory" est défini dans config/services.yaml ou .env
                $uploadDir = $this->getParameter('uploads_directory');
                $file->move($uploadDir, $newFilename);
                // Enregistrer le nom du fichier dans l'entité
                $comment->setAttachments($newFilename);
            } else {
                // Si aucun fichier n'est uploadé, on définit la valeur sur null
                $comment->setAttachments(null);
            }

        // Enregistrer le commentaire
        $entityManager->persist($comment);
        $entityManager->flush();

        // Rediriger vers la page du post
        return $this->redirectToRoute('app_forum_posts_show', ['id' => $forumPost->getId()]);
    }

    // Passer le post et le formulaire au template
    return $this->render('comments/new.html.twig', [
        'form' => $form->createView(),
        'forumPost' => $forumPost, // Passer le post au template
    ]);
}

    #[Route('/{id}', name: 'app_comments_show', methods: ['GET'])]
    public function show(Comments $comment): Response
    {
        return $this->render('comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comments_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        // Rediriger vers la page précédente ou une autre page
    return $this->redirectToRoute('app_forum_posts_show', ['id' => $comment->getForumPost()->getId()]);
    }
    // Route pour incrémenter les likes
    #[Route('/forum/posts/{postId}/like', name: 'app_forum_posts_like', methods: ['POST'])]
public function likePost(int $postId, ForumPostsRepository $forumPostsRepository, EntityManagerInterface $entityManager): Response
{
    $forumPost = $forumPostsRepository->find($postId);

    if (!$forumPost) {
        throw $this->createNotFoundException('Post not found');
    }

    // Incrémentation du nombre de likes
    $forumPost->incrementLikes(); // Assure-toi que la méthode `incrementLikes()` existe dans ton entity `ForumPosts`

    $entityManager->flush();

    return $this->redirectToRoute('app_forum_posts_show', ['id' => $postId]);
}

    // Création d'un commentaire lié à un post
    #[Route('/new/{postId}', name: 'app_comments_new_forum_post', methods: ['GET', 'POST'])]
    public function createComment(int $postId, ForumPostsRepository $forumPostsRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $forumPost = $entityManager->getRepository(ForumPosts::class)->find($postId);

        if (!$forumPost) {
            throw $this->createNotFoundException('Post not found');
        }

        $comment = new Comments();
        $comment->setForumPost($forumPost);

        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime()); // Ajouter la date de création
            $comment->setUpdatedAt(new \DateTime()); 

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comments_index');
        }

        return $this->render('comments/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
            'forumPost' => $forumPost,
        ]);
    }
    

}