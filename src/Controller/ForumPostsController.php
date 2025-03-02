<?php

namespace App\Controller;

use App\Service\BadWordsFilter;
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
use Symfony\Component\String\Slugger\SluggerInterface; 

#[Route('/forum/posts')]
final class ForumPostsController extends AbstractController{
    private BadWordsFilter $badWordsFilter;
    #[Route(name: 'app_forum_posts_index', methods: ['GET'])]
    public function index(Request $request, ForumPostsRepository $forumPostsRepository, PaginatorInterface $paginator): Response
    {
   // Récupérer les paramètres de recherche et de tri
   $search = $request->query->get('search');
    $sort = $request->query->get('sort', 'newest');
    $category = $request->query->get('category'); // Récupérer la catégorie sélectionnée

    // Récupérer les posts épinglés et les autres posts en fonction de la catégorie
    $pinnedPosts = $forumPostsRepository->findPinnedPosts($search, $sort, $category);
    // Récupérer les autres posts avec le tri spécifié
    $otherPosts = $forumPostsRepository->findBySearchAndSort($search, $sort, $category);

    // Combiner les résultats (posts épinglés en premier)
    $allPosts = array_merge($pinnedPosts, $otherPosts);

    // Paginer les résultats
    $forumPosts = $paginator->paginate(

        $allPosts,
        $request->query->getInt('page', 1), // Numéro de page
        5 // Nombre d'éléments par page
    );
    
        if ($request->isXmlHttpRequest()) {
            return $this->render('forum_posts/index.html.twig', [
                'forum_posts' => $forumPosts,
                'selected_category' => $category, // Passer la catégorie sélectionnée au template
            ]);
        }

        return $this->render('forum_posts/index.html.twig', [
            'forum_posts' => $forumPosts,
            'selected_category' => $category, // Passer la catégorie sélectionnée au template
        ]);
    }

    public function __construct(BadWordsFilter $badWordsFilter)
    {
        $this->badWordsFilter = $badWordsFilter;
    }

    #[Route('/new', name: 'app_forum_posts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $forumPost = new ForumPosts();
        $form = $this->createForm(ForumPostsType::class, $forumPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Filtrer les "bad words" dans le titre et le contenu
            $forumPost->setTitle($this->badWordsFilter->filter($forumPost->getTitle()));
            $forumPost->setContent($this->badWordsFilter->filter($forumPost->getContent()));
            $user = $entityManager->getRepository(User::class)->find(1);
            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé');
            } 
            $forumPost->setCreatedAt(new \DateTime()); 
            $forumPost->setUpdatedAt(new \DateTime());
            $attachment = $form->get('attachment')->getData();
            if ($attachment) {
                $originalFilename = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$attachment->guessExtension();


                
                    // Déplace le fichier dans le répertoire où vous souhaitez le stocker
                    $attachment->move(
                        $this->getParameter('uploads_directory'), // configurez ce paramètre dans `services.yaml`
                        $newFilename
                    );
                
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

            $this->addFlash('success', 'Le post a été créé avec succès.');
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
            // Filtrer les "bad words" dans le titre et le contenu
            $forumPost->setTitle($this->badWordsFilter->filter($forumPost->getTitle()));
            $forumPost->setContent($this->badWordsFilter->filter($forumPost->getContent()));
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
            $this->addFlash('success', 'Le post a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
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

    #[Route('/{id}/dislike', name: 'app_forum_posts_dislike', methods: ['POST'])]
    public function dislike(ForumPosts $forumPost, EntityManagerInterface $entityManager): Response
    {
        $forumPost->incrementDislikes();
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

    #[Route('/{id}/pin', name: 'app_forum_posts_pin', methods: ['POST'])]
    public function pin(ForumPosts $forumPost, EntityManagerInterface $entityManager): Response
    {
        $forumPost->setIsPinned(!$forumPost->isPinned());
        $entityManager->persist($forumPost);
        $entityManager->flush();

        $this->addFlash('success', $forumPost->isPinned() ? 'Post épinglé avec succès.' : 'Post désépinglé avec succès.');
        return $this->redirectToRoute('app_forum_posts_index');
    }
    #[Route('/forum/categories', name: 'app_forum_categories', methods: ['GET'])]
    public function categories(): Response
    {
        $categories = [
            'Covoiturage' => [
                'Expériences de covoiturage',
                'Budget & Partage des frais',
            ],
            'Véhicules' => [
                'Location de véhicules',
                'Entretien & Sécurité',
            ],
            'Conseils' => [
                'Astuces & Conseils',
            ],
        ];
    
        return $this->render('forum_posts/categories.html.twig', [
            'categories' => $categories,
        ]);
    }  
    
    #[Route('/forum/posts/category/{category}', name: 'app_forum_posts_by_category', methods: ['GET'])]
    public function postsByCategory(string $category, Request $request, ForumPostsRepository $forumPostsRepository, PaginatorInterface $paginator): Response
    {
        // Récupérer les posts de la catégorie spécifiée
        $forumPosts = $paginator->paginate(
            $forumPostsRepository->findByCategory($category),
            $request->query->getInt('page', 1), // Numéro de page
            5 // Nombre d'éléments par page
        );

        return $this->render('forum_posts/index.html.twig', [
            'forum_posts' => $forumPosts,
            'category' => $category,
        ]);
    }

}