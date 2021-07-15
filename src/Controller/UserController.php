<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Repository\StatsRepository;
use App\Repository\TaskCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController {


   private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
    $this->passwordEncoder = $passwordEncoder;        
    }

    /**
     * @Route("/admin/users", name="user.index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, UserRepository $userRepository, Request $request): Response {
        $users = $paginator->paginate(
            $userRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users/new", name="user.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword( 
                $this->passwordEncoder->encodePassword( $user, $user->getPassword() )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('user.index');
        }

        return $this->render('pages/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/users/{id}/edit", name="user.edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, User $user): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword( 
                $this->passwordEncoder->encodePassword( $user, $user->getPassword() )
            );
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès !');
            return $this->redirectToRoute('user.index');
        }

        return $this->render('pages/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="user.delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        }

        return $this->redirectToRoute('user.index');
    }

    /**
     * @Route("/profile", name="user.profile", methods={"GET"})
     * @param Security $security
     * @param StatsRepository $stats
     * @param TaskCategoryRepository $taskCategoryRepository
     * @return Response
     */
    public function profile(Security $security, StatsRepository $stats, TaskCategoryRepository $taskCategoryRepository): Response {
        $stats->setObjectManager($this->getDoctrine()->getManager());

        $tasksCategory = $taskCategoryRepository->findAll();

        return $this->render('pages/user/profile/show.html.twig', [
            'user' => $security->getUser(),
            'stat3' => $stats->req3(date("n/Y"), $tasksCategory, $security->getUser()->getId()),
            'tcategs' => $tasksCategory,
        ]);
    }

    /**
     * @Route("/profile/edit", name="user.profile.edit", methods={"GET","POST"})
     * @param Security $security
     * @param Request $request
     * @return Response
     */
    public function editProfile(Security $security, Request $request): Response {
        $form = $this->createForm(ProfileType::class, $security->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user.profile');
        }

        return $this->render('pages/user/profile/edit.html.twig', [
            'user' => $security->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
