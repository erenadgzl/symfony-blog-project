<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Galery;
use App\Entity\Message;
use App\Entity\Post;
use App\Entity\Setting;
use App\Entity\TagCloud;
use App\Entity\Users;
use App\Entity\Image;
use App\Form\CommentType;
use App\Form\GaleryType;
use App\Form\MessageType;
use App\Form\PostType;
use App\Form\TagCloudType;
use App\Form\Users1Type;
use App\Form\ImageType;
use App\Repository\CommentRepository;
use App\Repository\GaleryRepository;
use App\Repository\MessageRepository;
use App\Repository\PostRepository;
use App\Repository\SettingRepository;
use App\Repository\TagCloudRepository;
use App\Repository\TagRepository;
use App\Repository\UsersRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contactusmessage;
use App\Form\ContactusmessageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(SettingRepository $settingRepository,UserPasswordEncoderInterface $passwordEncoder,PostRepository $postRepository,GaleryRepository $galeryRepository,TagRepository $tagRepository)
    {

        $data=$settingRepository->findAll();
        if(!$data){
            $setting=new Setting();
            $em=$this->getDoctrine()->getManager();
            $setting->setTitle('Site');
            $em->persist($setting);
            $em->flush();
            $admin=new Users();
            $admin->setName('admin');
            $admin->setEmail('admin@admin');
            $password = $passwordEncoder->encodePassword($admin, '123456');
            $admin->setPassword($password);
            $admin->setRoles("ROLE_ADMIN");
            $em=$this->getDoctrine()->getManager();
            $em->persist($admin);
            $em->flush();
            $data=$settingRepository->findAll();
        }
        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        $postlar=$postRepository->findAll();
        $postsayisi=count($postlar);

        $posts=$postRepository->findBy(array('status'=>1), array(),12);
        $tags=$tagRepository->findAll();
        $popularposts=$postRepository->findBy(array('status'=>1), array('hit' => 'desc'),6);
        $galeri=$galeryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'data' => $data[0],
            'categories' => $results,
            'posts' => $posts,
            'popularposts' => $popularposts,
            'galleries' => $galeri,
            'tags' => $tags,
            'postsayisi' => $postsayisi
        ]);
    }
    /**
     * @Route("/home/{page}", name="pagesAction")
     */
    public function pagesAction($page,SettingRepository $settingRepository,GaleryRepository $galeryRepository,PostRepository $postRepository,TagRepository $tagRepository)
    {
        $data=$settingRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        $postlar=$postRepository->findAll();
        $postsayisi=count($postlar);

        $tags=$tagRepository->findAll();
        $galeri=$galeryRepository->findAll();
        $posts=$postRepository->findBy(array('status'=>1), array(),12,12 * ($page - 1));
        $popularposts=$postRepository->findBy(array('status'=>1), array('hit' => 'desc'),6);
        return $this->render('home/index.html.twig', [
            'data' => $data[0],
            'categories' => $results,
            'posts' => $posts,
            'popularposts' => $popularposts,
            'galleries'=>$galeri,
            'tags' => $tags,
            'postsayisi' => $postsayisi
        ]);
    }

    /**
     * @Route("/hakkimizda", name="hakkimizda")
     */
    public function hakkimizda(SettingRepository $settingRepository,GaleryRepository $galeryRepository,PostRepository $postRepository,TagRepository $tagRepository)
    {
        $data=$settingRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        $galeri=$galeryRepository->findAll();
        $tags=$tagRepository->findAll();
        $popularposts=$postRepository->findBy(array('status'=>1), array('hit' => 'desc'),6);
        return $this->render('home/hakkimizda.html.twig', [

            'categories' => $results,
            'data' => $data[0],
            'popularposts' => $popularposts,
            'galleries'=>$galeri,
            'tags'=> $tags
        ]);
    }
    /**
     * @Route("/iletisim", name="iletisim", methods={"GET","POST"})
     */
    public function iletisim(Request $request,SettingRepository $settingRepository,GaleryRepository $galeryRepository,PostRepository $postRepository,TagRepository $tagRepository)
    {

        $data=$settingRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        $galeri=$galeryRepository->findAll();
        $tags=$tagRepository->findAll();
        $popularposts=$postRepository->findBy(array('status'=>1), array('hit' => 'desc'),6);

        $contactusmessage = new Contactusmessage();
        $form = $this->createForm(ContactusmessageType::class, $contactusmessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactusmessage->setStatus(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contactusmessage);
            $entityManager->flush();

            return $this->redirectToRoute('iletisim');
        }



        return $this->render('home/iletisim.html.twig', [
            'categories' => $results,
            'data' => $data[0],
            'popularposts' => $popularposts,
            'galleries'=>$galeri,
            'tags' => $tags,
            'contactusmessage' => $contactusmessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/kategori/{id}/{page}", name="kategori")
     */
    public function kategori($id,$page,SettingRepository $settingRepository,GaleryRepository $galeryRepository,PostRepository $postRepository,TagRepository $tagRepository)
    {
        $postlar=$postRepository->findBy(array('category'=>$id));
        $postsayisi=count($postlar);

        $data=$settingRepository->findAll();
        $posts=$postRepository->findBy(array('category'=>$id , 'status'=>1),array(),12,12 * ($page - 1));

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        $galeri=$galeryRepository->findAll();
        $tags=$tagRepository->findAll();
        $popularposts=$postRepository->findBy(array('status'=>1), array('hit' => 'desc'),6);
        return $this->render('home/kategori.html.twig', [
            'data' => $data[0],
            'categories' => $results,
            'posts' => $posts,
            'popularposts' => $popularposts,
            'categoryID' => $id,
            'galleries'=>$galeri,
            'tags' => $tags,
            'postsayisi' => $postsayisi

        ]);
    }

    /**
     * @Route("/post/{id}", name="post", methods={"GET","POST"})
     */
    public function post($id,Request $request,CommentRepository $commentRepository,GaleryRepository $galeryRepository,SettingRepository $settingRepository,PostRepository $postRepository,TagCloudRepository $tagCloudRepository,TagRepository $tagRepository)
    {


        $data=$settingRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        $post=$postRepository->find($id); $post->setHit(($post->getHit()+1));
        $em->persist($post);
        $em->flush();

        $tagsc=$tagCloudRepository->findBy(array('post'=>$id));
        $comments=$commentRepository->findBy(array('post'=>$id));
        $galeri=$galeryRepository->findAll();
        $tags=$tagRepository->findAll();
        $popularposts=$postRepository->findBy(array('status'=>1), array('hit' => 'desc'),6);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $comments=$commentRepository->findBy(array('post'=>$id));

            return $this->render('home/post.html.twig', [

                'categories' => $results,
                'data' => $data[0],
                'post' => $post,
                'tagscloud' => $tagsc,
                'comments' => $comments,
                'popularposts' => $popularposts,
                'galleries'=>$galeri,
                'tags' => $tags,
                'form' => $form->createView(),
            ]);
        }


        return $this->render('home/post.html.twig', [

            'categories' => $results,
            'data' => $data[0],
            'post' => $post,
            'tagscloud' => $tagsc,
            'comments' => $comments,
            'popularposts' => $popularposts,
            'galleries'=>$galeri,
            'tags' => $tags,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/galeri/{id}", name="galeri")
     */
    public function galeri($id,SettingRepository $settingRepository,PostRepository $postRepository,GaleryRepository $galeryRepository,TagRepository $tagRepository)
    {
        $data=$settingRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        $tags=$tagRepository->findAll();
        $galeri=$galeryRepository->findAll();
        $galeriSelected=$galeryRepository->findBy(array('part'=>$id));
        $popularposts=$postRepository->findBy(array(), array('hit' => 'desc'),6);

        return $this->render('home/galeri.html.twig', [

            'categories' => $results,
            'data' => $data[0],
            'galleries' => $galeri,
            'popularposts' => $popularposts,
            'galeriSelected' => $galeriSelected,
            'tags' => $tags
        ]);
    }

    /**
     * @Route("/tags/{id}/{page}", name="tag")
     */
    public function tag($id,$page,SettingRepository $settingRepository,TagCloudRepository $tagCloudRepository,PostRepository $postRepository,GaleryRepository $galeryRepository,TagRepository $tagRepository)
    {

        $postlar=$tagCloudRepository->findBy(array('tag'=>$id));
        $postsayisi=count($postlar);

        $data=$settingRepository->findAll();
        $posts=$tagCloudRepository->findBy(array('tag'=>$id),array(),12,12 * ($page - 1));

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        $galeri=$galeryRepository->findAll();
        $tags=$tagRepository->findAll();
        $popularposts=$postRepository->findBy(array('status'=>1), array('hit' => 'desc'),6);
        return $this->render('home/tags.html.twig', [
            'data' => $data[0],
            'categories' => $results,
            'posts' => $posts,
            'popularposts' => $popularposts,
            'categoryID' => $id,
            'galleries'=>$galeri,
            'tags' => $tags,
            'postsayisi' => $postsayisi
        ]);
    }
    /**
     * @Route("/profile/message", name="profile_message", methods={"GET","POST"})
     */
    public function profile(Request $request,MessageRepository $messageRepository,UsersRepository $usersRepository,SettingRepository $settingRepository,PostRepository $postRepository,GaleryRepository $galeryRepository,TagRepository $tagRepository)
    {
        $ADMIN=$usersRepository->findOneBy(array('roles'=>'ROLE_ADMIN'));

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $girisyapan=$this->getUser();

        $sqql="SELECT * FROM message WHERE (sender_id =".$girisyapan->getId()." ) or (receiver_id =".$girisyapan->getId()." )  ";
        $statementt = $connection->prepare($sqql);
        $statementt->execute();
        $resultsmessage = $statementt->fetchAll();



        $data=$settingRepository->findAll();



        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        $galeri=$galeryRepository->findAll();
        $tags=$tagRepository->findAll();
        $popularposts=$postRepository->findBy(array(), array('hit' => 'desc'),6);


        $m = new Message();
        $form = $this->createForm(MessageType::class, $m);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($m);
            $entityManager->flush();

            return $this->redirectToRoute('profile_message');
        }

        return $this->render('home/profil.html.twig', [
            'data' => $data[0],
            'categories' => $results,
            'popularposts' => $popularposts,
            'galleries'=>$galeri,
            'tags' => $tags,
            'adminid'=>$ADMIN->getId(),
            'form' => $form->createView(),
            'mesajlar' => $resultsmessage,
        ]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit", methods={"GET","POST"})
     */
    public function profile_edit(SettingRepository $settingRepository,Request $request,UserPasswordEncoderInterface $passwordEncoder,UsersRepository $usersRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $girisyapan=$this->getUser();

        $data=$settingRepository->findAll();

        $user=$usersRepository->findOneBy(array('id'=>$girisyapan->getId()));

        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);


        if ($request->isMethod('post') ) {

            if($user->getPassword() === "password"){
                $em=$this->getDoctrine()->getManager();
                $connection = $em->getConnection();

                $sql="SELECT * FROM users WHERE id = ".$girisyapan->getId();
                $statement = $connection->prepare($sql);
                $statement->execute();
                $results = $statement->fetchAll();

                $user->setPassword($results[0]['password']);
            }else{
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->render('home/profiledit.html.twig', [
                'data' => $data[0],
                'user' => $user,
                'form' => $form->createView()
            ]);
        }



        return $this->render('home/profiledit.html.twig', [
            'data' => $data[0],
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/profile/post", name="profile_post", methods={"GET"})
     */
    public function profile_post(SettingRepository $settingRepository,PostRepository $postRepository,UsersRepository $usersRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $girisyapan=$this->getUser();

        $data=$settingRepository->findAll();

        $user=$usersRepository->findOneBy(array('id'=>$girisyapan->getId()));


        return $this->render('home/userpost/userpostadd.html.twig', [
            'data' => $data[0],
            'user' => $user,
            'posts'=> $postRepository->findBy(array('user'=>$girisyapan->getId())),
        ]);
    }
    /**
     * @Route("/profile/post/new", name="user_post_new", methods={"GET","POST"})
     */
    public function profile_post_new(SettingRepository $settingRepository,Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $data=$settingRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setCreatedAt(new \DateTime('now'));
            $post->setStatus(0);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('profile_post');
        }

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        return $this->render('home/userpost/userpostnew.html.twig', [
            'post' => $post,
            'data' => $data[0],
            'form' => $form->createView(),
            'categories' => $results
        ]);
    }

    /**
     * @Route("profile/post/{id}/edit", name="user_post_edit", methods={"GET","POST"})
     */
    public function profile_post_edit(SettingRepository $settingRepository,Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setStatus(0);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_post');
        }

        $data=$settingRepository->findAll();

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql="SELECT * FROM category";
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        return $this->render('home/userpost/userpostedit.html.twig', [
            'post' => $post,
            'data' => $data[0],
            'form' => $form->createView(),
            'categories' => $results
        ]);
    }
    /**
     * @Route("profile/post/{id}/gallery", name="post_galery_index", methods={"GET","POST"})
     */
    public function post_galery_index($id,GaleryRepository $galeryRepository,Request $request): Response
    {


        $galery = new Galery();
        $form = $this->createForm(GaleryType::class, $galery);
        $form->handleRequest($request);
        $file=$request->files->get('imagename');

        if ($request->isMethod('post') && $file!=null ) {
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $entityManager = $this->getDoctrine()->getManager();
            $galery->setName($fileName);
            $entityManager->persist($galery);
            $entityManager->flush();


            $em=$this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $sql="SELECT * FROM galery WHERE part=".$id;
            $statement = $connection->prepare($sql);
            $statement->execute();
            $results = $statement->fetchAll();
            return $this->render('home/userpost/userpostgallery.html.twig', ['galeries' => $results,'postid' => $id
            ]);
        }

        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql="SELECT * FROM galery WHERE part=".$id;
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
        return $this->render('home/userpost/userpostgallery.html.twig', ['galeries' => $results,'postid' => $id
        ]);
    }
    /**
     * @Route("profile/post/{id}/image", name="post_image_index", methods={"GET","POST"})
     */
    public function post_image_index($id,ImageRepository $imageRepository,Request $request): Response
    {


        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        $file=$request->files->get('imagename');

        if ($request->isMethod('post') && $file!=null ) {
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $entityManager = $this->getDoctrine()->getManager();
            $image->setName($fileName);
            $entityManager->persist($image);
            $entityManager->flush();


            $em=$this->getDoctrine()->getManager();
            $connection = $em->getConnection();

            $sql="UPDATE post SET short_content='/uploads/images/".$fileName."' WHERE id=".$id;
            $statement = $connection->prepare($sql);
            $statement->execute();


            $em=$this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $sql="SELECT * FROM post WHERE id=".$id;
            $statement = $connection->prepare($sql);
            $statement->execute();
            $results = $statement->fetchAll();

            return $this->render('home/userpost/userpostimage.html.twig', ['post' => $results,'postid' => $id
            ]);
        }
        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql="SELECT * FROM post WHERE id=".$id;
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        return $this->render('home/userpost/userpostimage.html.twig', ['post' => $results,'postid' => $id
        ]);
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    /**
     * @Route("profile/post/{id}/tagcloud", name="post_tagcloud_index", methods={"GET","POST"})
     */
    public function post_tagcloud_index($id,TagRepository $tagRepository,Request $request): Response
    {


        $tagcloud = new TagCloud();
        $form = $this->createForm(TagCloudType::class, $tagcloud);
        $form->handleRequest($request);

        if ($request->isMethod('post')  ) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tagcloud);
            $entityManager->flush();



            $em=$this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $sql="SELECT * FROM tag_cloud WHERE post_id=".$id;
            $statement = $connection->prepare($sql);
            $statement->execute();
            $results = $statement->fetchAll();

            return $this->render('home/userpost/userposttagcloud.html.twig', ['post' => $results,'postid' => $id,
                'tags' => $tagRepository->findAll(),
            ]);
        }
        $em=$this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql="SELECT * FROM tag_cloud WHERE post_id=".$id;
        $statement = $connection->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        return $this->render('home/userpost/userposttagcloud.html.twig', ['post' => $results,'postid' => $id,
            'tags' => $tagRepository->findAll(),
        ]);
    }
}
