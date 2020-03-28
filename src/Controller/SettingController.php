<?php

namespace App\Controller;

use App\Entity\Contactusmessage;
use App\Entity\Setting;
use App\Form\SettingType;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/setting")
 */
class SettingController extends AbstractController
{
    /**
     * @Route("/", name="setting_index", methods={"GET"})
     */
    public function index(SettingRepository $settingRepository): Response
    {
        $data=$settingRepository->findAll();
        if(!$data){
            $setting=new Setting();
            $em=$this->getDoctrine()->getManager();
            $setting->setTitle('Site');
            $em->persist($setting);
            $em->flush();
            $data=$settingRepository->findAll();
        }

        return $this->render('setting/index.html.twig', ['settings' => $settingRepository->findAll(),
            'countmessage' => $this->getcountmessages()
            ]);
    }

    /**
     * @Route("/new", name="setting_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $setting = new Setting();
        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($setting);
            $entityManager->flush();

            return $this->redirectToRoute('setting_index',['countmessage' => $this->getcountmessages()]);
        }

        return $this->render('setting/new.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="setting_show", methods={"GET"})
     */
    public function show(Setting $setting): Response
    {
        return $this->render('setting/show.html.twig', ['setting' => $setting,
            'countmessage' => $this->getcountmessages()
            ]);
    }

    /**
     * @Route("/{id}/edit", name="setting_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Setting $setting): Response
    {
        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',' Güncelleme Başarılı');
            return $this->redirectToRoute('setting_index', ['id' => $setting->getId(),
                'countmessage' => $this->getcountmessages()
                ]);
        }

        return $this->render('setting/edit.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="setting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Setting $setting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$setting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($setting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('setting_index',['countmessage' => $this->getcountmessages()]);
    }
    public function getcountmessages(): int
    {
        $em = $this->getDoctrine()->getManager();
        $messages=$em->getRepository(Contactusmessage::class)->findBy(array('status'=>0));

        return sizeof($messages);
    }
}
