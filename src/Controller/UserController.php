<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;


class UserController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Route("/user", methods="GET", name="user")
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $current_page = $request->query->getInt('page', 1);
        $users = $userRepository->getItems($request);
        $count = count($users->getResult());
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users,
            $current_page,
            5
        );

        $result = [
            'count' => $count,
            'items' => $pagination,
            'links' => [
            ]
        ];

        $params = $request->query->all(); 
        $previous_page = $current_page - 1;
        if ($previous_page >= 1) {
            $params['page'] = $previous_page;
            $result['links']['previous'] = sprintf('/api/user?%s', http_build_query($params));
        }

        $next_page = $current_page + 1;
        if ($next_page <= ($count / 5)) {
            $params['page'] = $next_page;
            $result['links']['next'] = sprintf('/api/user?%s', http_build_query($params));
        }

        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/user/create", methods="POST", name="user_create")
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($form->getData());
            $em->flush();
            return $this->view(['success' => true, 'id' => $user->getId()], Response::HTTP_CREATED);
        }

        return $this->view([$form->getErrors(true)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/user/{id}", methods="GET", name="user_show")
     */
    public function show($id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);

        if ($user === null) {
            return $this->notFound();
        }

        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Route("/user/{id}", methods="PUT", name="user_edit")
     */
    public function update($id, Request $request, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $user = $userRepository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->merge($form->getData());
            $em->flush();
            return $this->redirectToRoute("user_show", [ 'id' => $user->getId() ]);
        }

        return $this->view([$form->getErrors(true)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/user/{id}", methods="DELETE", name="user_destroy")
     */
    public function destroy($id, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $user = $userRepository->find($id);

        if ($user === null) {
            return $this->notFound();
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user');
    }

    private function notFound() 
    {
        return $this->view(['error' => true, 'messages' => ['NOT FOUND']], Response::HTTP_NOT_FOUND);
    }
}
