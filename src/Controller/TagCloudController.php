<?php

namespace App\Controller;

use App\Entity\Contactusmessage;
use App\Entity\TagCloud;
use App\Form\TagCloudType;
use App\Repository\TagCloudRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/tag/cloud")
 */
class TagCloudController extends AbstractController
{
    /**
     * @Route("/", name="tag_cloud_index", methods={"GET"})
     */
    public function index(TagCloudRepository $tagCloudRepository): Response
    {
        return $this->render('tag_cloud/index.html.twig', ['tag_clouds' => $tagCloudRepository->findAll(),
            'countmessage' => $this->getcountmessages()
            ]);
    }

    /**
     * @Route("/new", name="tag_cloud_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tagCloud = new TagCloud();
        $form = $this->createForm(TagCloudType::class, $tagCloud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tagCloud);
            $entityManager->flush();

            return $this->redirectToRoute('tag_cloud_index',['countmessage' => $this->getcountmessages()]);
        }

        return $this->render('tag_cloud/new.html.twig', [
            'tag_cloud' => $tagCloud,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="tag_cloud_show", methods={"GET"})
     */
    public function show(TagCloud $tagCloud): Response
    {
        return $this->render('tag_cloud/show.html.twig', ['tag_cloud' => $tagCloud,
            'countmessage' => $this->getcountmessages()
            ]);
    }

    /**
     * @Route("/{id}/edit", name="tag_cloud_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TagCloud $tagCloud): Response
    {
        $form = $this->createForm(TagCloudType::class, $tagCloud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tag_cloud_index', ['id' => $tagCloud->getId(),
                'countmessage' => $this->getcountmessages()
            ]);
        }

        return $this->render('tag_cloud/edit.html.twig', [
            'tag_cloud' => $tagCloud,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="tag_cloud_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TagCloud $tagCloud): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tagCloud->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tagCloud);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tag_cloud_index',['countmessage' => $this->getcountmessages()]);
    }

    public function getcountmessages(): int
    {
        $em = $this->getDoctrine()->getManager();
        $messages=$em->getRepository(Contactusmessage::class)->findBy(array('status'=>0));

        return sizeof($messages);
    }
}
