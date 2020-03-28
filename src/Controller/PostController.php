<?php

namespace App\Controller;

use App\Entity\Contactusmessage;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', ['posts' => $postRepository->findBy(array(), array('id' => 'desc')),'countmessage' => $this->getcountmessages()]);
    }

    /**
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setCreatedAt(new \DateTime('now'));
            $post->setStatus(0);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index',['countmessage' => $this->getcountmessages()]);
        }


        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages(),
            'categories'=>$results
        ]);
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', ['post' => $post,
            'countmessage' => $this->getcountmessages()]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index', ['id' => $post->getId(),'countmessage' => $this->getcountmessages()]);
        }

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages(),
            'categories'=>$results
        ]);
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index',['countmessage' => $this->getcountmessages()]);
    }

    public function getcountmessages(): int
    {
        $em = $this->getDoctrine()->getManager();
        $messages=$em->getRepository(Contactusmessage::class)->findBy(array('status'=>0));

        return sizeof($messages);
    }


    /**
     * @Route("/{id}/statu", name="post_statu_index", methods={"GET","POST"})
     */
    public function post_statu_index($id,PostRepository $postRepository,Request $request): Response
    {


        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="UPDATE post SET status=0 WHERE id=".$id;
        $statement = $connection->prepare($sql);
        $statement->execute();




        return $this->render('post/index.html.twig', ['posts' => $postRepository->findBy(array(), array('id' => 'desc')),'countmessage' => $this->getcountmessages()
        ]);
    }
    /**
     * @Route("/{id}/status", name="post_statu_index2", methods={"GET","POST"})
     */
    public function post_statu2_index($id,PostRepository $postRepository,Request $request): Response
    {


        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="UPDATE post SET status=1 WHERE id=".$id;
        $statement = $connection->prepare($sql);
        $statement->execute();




        return $this->render('post/index.html.twig', ['posts' => $postRepository->findBy(array(), array('id' => 'desc')),'countmessage' => $this->getcountmessages()
        ]);
    }
}
