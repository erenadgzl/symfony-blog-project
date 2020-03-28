<?php

namespace App\Controller;

use App\Entity\Contactusmessage;
use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $messages=$em->getRepository(Contactusmessage::class)->findBy(array('status'=>0));


        return $this->render('backend.html.twig', [
            'controller_name' => 'AdminController',
            'countmessage' => sizeof($messages),
        ]);
    }


}
