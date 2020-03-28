<?php

namespace App\Controller;

use App\Entity\Contactusmessage;
use App\Form\ContactusmessageType;
use App\Repository\ContactusmessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/contactusmessage")
 */
class ContactusmessageController extends AbstractController
{
    /**
     * @Route("/", name="contactusmessage_index", methods={"GET"})
     */
    public function index(ContactusmessageRepository $contactusmessageRepository): Response
    {
        return $this->render('contactusmessage/index.html.twig', ['contactusmessages' => $contactusmessageRepository->findAll(),
            'countmessage' => $this->getcountmessages()
            ]);
    }

    /**
     * @Route("/new", name="contactusmessage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $contactusmessage = new Contactusmessage();
        $form = $this->createForm(ContactusmessageType::class, $contactusmessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contactusmessage);
            $entityManager->flush();

            return $this->redirectToRoute('contactusmessage_index',['countmessage' => $this->getcountmessages()]);
        }

        return $this->render('contactusmessage/new.html.twig', [
            'contactusmessage' => $contactusmessage,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="contactusmessage_show", methods={"GET"})
     */
    public function show($id,Contactusmessage $contactusmessage,ContactusmessageRepository $contactusmessageRepository): Response
    {
        $em=$this->getDoctrine()->getManager();
        $message=$contactusmessageRepository->find($id); $message->setStatus(1);
        $em->persist($message);
        $em->flush();

        return $this->render('contactusmessage/show.html.twig', ['contactusmessage' => $contactusmessage,'countmessage' => $this->getcountmessages()]);
    }

    /**
     * @Route("/{id}/edit", name="contactusmessage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Contactusmessage $contactusmessage): Response
    {
        $form = $this->createForm(ContactusmessageType::class, $contactusmessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contactusmessage_index', ['id' => $contactusmessage->getId(),
                'countmessage' => $this->getcountmessages()
                ]);
        }

        return $this->render('contactusmessage/edit.html.twig', [
            'contactusmessage' => $contactusmessage,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="contactusmessage_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Contactusmessage $contactusmessage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contactusmessage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contactusmessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contactusmessage_index',['countmessage' => $this->getcountmessages()]);
    }
    public function getcountmessages(): int
    {
        $em = $this->getDoctrine()->getManager();
        $messages=$em->getRepository(Contactusmessage::class)->findBy(array('status'=>0));

        return sizeof($messages);
    }

}
