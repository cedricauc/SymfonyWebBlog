<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="app_post")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Post::class)->findAll();

        return $this->render('post/index.html.twig', [
            'controller_name' => 'Post index',
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/{id}", methods="GET", name="app_post_show", requirements={"postgId"="\d+"})
     */
    public function show(EntityManagerInterface $entityManager, $postId = null): Response
    {
        $posts = $entityManager->getRepository(Post::class)->findAll();

        if (!empty($postId)) {
            $currentPost = $entityManager->getRepository(Post::class)->find($postId);
        }

        if (empty($currentPost)) {
            $currentPost = current($posts);
        }

        return $this->render('post/show.html.twig', [
            'currentPost' => $currentPost
        ]);
    }

    /**
     * @Route("/new", methods={"GET","POST"}, name="app_post_create")
     */
    public function create(EntityManagerInterface $entityManager, Request $request) : Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post');
        }

        return $this->render('create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/edit/{id}", methods={"GET","POST"}, name="app_post_edit")
     */
    public function edit(EntityManagerInterface $entityManager, Request $request, Post $post) : Response
    {
        if (!$this->isGranted('POST_EDIT', $post)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post');
        }

        return $this->render('post/edit.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", methods={"GET","POST"}, name="app_post_delete")
     */
    public function delete(EntityManagerInterface $entityManager, $postId = null): Response
    {
        $post = $entityManager->getRepository(Listing::class)->find($postId);

        if (empty($post)) {
            $this->addFlash(
                "warning",
                "Impossible de supprimer le post"
            );
        } else {
            $entityManager->remove($post);
            $entityManager->flush();

            $title = $post->getTitle();

            $this->addFlash(
                "success",
                "Le post « $title » a été supprimée avec succès"
            );
        }

        return $this->redirectToRoute('app_post');
    }

}
