<?php

namespace App\Controller;

use App\Entity\Contactusmessage;
use App\Entity\Users;
use App\Form\Users1Type;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("admin/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository): Response
    {
        return $this->render('users/index.html.twig', ['users' => $usersRepository->findAll(),'countmessage' => $this->getcountmessages()]);
    }

    /**
     * @Route("/new", name="users_new", methods={"GET","POST"})
     */
    public function new(Request $request,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Users();
        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles("ROLE_USER");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users_index',['countmessage' => $this->getcountmessages()]);
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="users_show", methods={"GET"})
     */
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', ['user' => $user,'countmessage' => $this->getcountmessages()]);
    }

    /**
     * @Route("/{id}/edit", name="users_edit", methods={"GET","POST"})
     */
    public function edit($id,Request $request, Users $user,UserPasswordEncoderInterface $passwordEncoder,UsersRepository $usersRepository): Response
    {
        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);


        if ($request->isMethod('post') ) {

            if($user->getPassword() === "password"){
                $em=$this->getDoctrine()->getManager();
                $connection = $em->getConnection();

                $sql="SELECT * FROM users WHERE id = ".$id;
                $statement = $connection->prepare($sql);
                $statement->execute();
                $results = $statement->fetchAll();

                $user->setPassword($results[0]['password']);
            }else{
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('users_index', ['id' => $user->getId(),'countmessage' => $this->getcountmessages()]);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'countmessage' => $this->getcountmessages()
        ]);
    }

    /**
     * @Route("/{id}", name="users_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('users_index',['countmessage' => $this->getcountmessages()]);
    }

    public function getcountmessages(): int
    {
        $em = $this->getDoctrine()->getManager();
        $messages=$em->getRepository(Contactusmessage::class)->findBy(array('status'=>0));

        return sizeof($messages);
    }

    /**
     * @Route("/{id}/yetki", name="admin_yap", methods={"GET","POST"})
     */
    public function admin_yap($id,Request $request, Users $user,UserPasswordEncoderInterface $passwordEncoder,UsersRepository $usersRepository): Response
    {
        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);

        $em=$this->getDoctrine()->getManager();

        $sql="UPDATE users SET roles='ROLE_ADMIN' WHERE id = ".$id;
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();





        return $this->redirectToRoute('users_index', ['users' => $usersRepository->findAll(),'countmessage' => $this->getcountmessages()]);
    }
    /**
     * @Route("/{id}/yetki_al", name="user_yap", methods={"GET","POST"})
     */
    public function user_yap($id,Request $request, Users $user,UserPasswordEncoderInterface $passwordEncoder,UsersRepository $usersRepository): Response
    {
        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);

        $em=$this->getDoctrine()->getManager();

        $sql="UPDATE users SET roles='ROLE_USER' WHERE id = ".$id;
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();





        return $this->redirectToRoute('users_index', ['users' => $usersRepository->findAll(),'countmessage' => $this->getcountmessages()]);
    }
}
