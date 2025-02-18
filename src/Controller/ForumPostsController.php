<?php

namespace App\Controller;

use App\Entity\ForumPosts;
use App\Entity\User; 
use App\Form\ForumPostsType;
use App\Repository\ForumPostsRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/forum/posts')]
final class ForumPostsController extends AbstractController{
    #[Route(name: 'app_forum_posts_index', methods: ['GET'])]
    public function index(Request $request, ForumPostsRepository $forumPostsRepository, PaginatorInterface $paginator): Response
    {
        $categories = [
            'Covoiturage' => [
                'Trajets réguliers',
                'Trajets occasionnels',
                'Recherche de covoiturage',
            ],
            'Location de véhicules' => [
                'Vélos électriques',
                'Voitures électriques',
            ],
        ];
   // Récupérer les paramètres de recherche et de tri
   $search = $request->query->get('search');
    $sort = $request->query->get('sort', 'newest');

    // Créer la requête de base pour filtrer les posts
    /*$queryBuilder = $forumPostsRepository->createQueryBuilder('fp')
        ->andWhere('fp.title LIKE :search OR fp.content LIKE :search')
        ->setParameter('search', '%' . $search . '%');
*/
    // Appliquer le tri en fonction du paramètre
   


    // Paginer les résultats
    $forumPosts = $paginator->paginate(

         $forumPostsRepository->findBySearchAndSort($search, $sort), 
        $request->query->getInt('page', 1), // Numéro de page
        10 // Nombre d'éléments par page
    );
    /*$forumPosts = $queryBuilder->getQuery()->getResult();
        // Appeler la méthode du repository pour filtrer et trier les posts
        $forumPosts = $forumPostsRepository->findBySearchAndSort($search, $sort);
        $forumPosts = $forumPostsRepository->findAll();
        dump($forumPosts); // Ajoutez cette ligne pour déboguer
        $forumPosts = $forumPostsRepository->createQueryBuilder('p')
        ->leftJoin('p.comments', 'c')
        ->addSelect('c')
        ->getQuery()
        ->getResult();*/

        return $this->render('forum_posts/index.html.twig', [
            'forum_posts' => $forumPosts,
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_forum_posts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forumPost = new ForumPosts();
        $form = $this->createForm(ForumPostsType::class, $forumPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository(User::class)->find(1);
            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé');
            } 
            $forumPost->setCreatedAt(new \DateTime()); 
            $forumPost->setUpdatedAt(new \DateTime());
            $attachment = $form->get('attachment')->getData();
            if ($attachment instanceof UploadedFile) {
                $newFilename = uniqid().'.'.$attachment->guessExtension();

                try {
                    // Déplace le fichier dans le répertoire où vous souhaitez le stocker
                    $attachment->move(
                        $this->getParameter('uploads_directory'), // configurez ce paramètre dans `services.yaml`
                        $newFilename
                    );
                    dump('Fichier déplacé avec succès');
                } catch (\Exception $e) {
                    dump('Erreur lors du déplacement du fichier : ' . $e->getMessage());
                    // Gestion des erreurs
                    $this->addFlash('error', 'File upload failed!');
                    return $this->redirectToRoute('forum_post_new');
                }

                // Enregistrez le chemin du fichier dans l'entité
                $forumPost->setAttachment($newFilename);
            }
            $forumPost->setUser($user); // Associer l'utilisateur au post
            $forumPost->setCreatedAt(new \DateTime());
            $forumPost->setUpdatedAt(new \DateTime());

            $entityManager->persist($forumPost);
            $entityManager->flush();

            $forumPost->updateCommentsCount();
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum_posts/new.html.twig', [
            'forum_post' => $forumPost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_posts_show', methods: ['GET'])]
    public function show(ForumPosts $forumPost, CommentsRepository $commentsRepository): Response
    {
        // Récupérer les commentaires associés au post
        $comments = $commentsRepository->findBy(['forumPost' => $forumPost]);

        return $this->render('forum_posts/show.html.twig', [
            'forum_post' => $forumPost,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forum_posts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ForumPosts $forumPost, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForumPostsType::class, $forumPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumPost->setUpdatedAt(new \DateTime());
            $entityManager->flush();
            
            $forumPost->updateCommentsCount();
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forum_posts/edit.html.twig', [
            'forum_post' => $forumPost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forum_posts_delete', methods: ['POST'])]
    public function delete(Request $request, ForumPosts $forumPost, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forumPost->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($forumPost);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_posts_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/like', name: 'app_forum_posts_like', methods: ['POST'])]
    public function like(ForumPosts $forumPost, EntityManagerInterface $entityManager): Response
    {
        $forumPost->incrementLikes();
        $entityManager->persist($forumPost);
        $entityManager->flush();

        return $this->redirectToRoute('app_forum_posts_index', ['id' => $forumPost->getId()]);
    }

    #[Route('/admin/posts', name: 'app_admin_posts')]
    public function adminPosts(ForumPostsRepository $forumPostsRepository): Response
    {
        // Récupérer tous les posts avec leurs commentaires
        $forumPosts = $forumPostsRepository->findAllWithComments();

        return $this->render('admin/posts.html.twig', [
            'forum_posts' => $forumPosts,
        ]);
    }
        

}