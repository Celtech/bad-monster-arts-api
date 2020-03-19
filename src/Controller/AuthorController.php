<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthorController extends AbstractController
{
    private $em;
    private $passwordEncoder;

    /**
     * PostsController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/api/author", name="author_make", methods={"POST"})
     */
    public function makeAuthor(Request $request)
    {
        $data = json_decode($request->getContent());
        $author = new User();
        $author->setFirstName($data->first_name);
        $author->setLastName($data->last_name);
        $author->setEmail($data->email);
        $author->setRoles([]);
        $author->setPassword($this->passwordEncoder->encodePassword($author, $data->password));

        $this->em->persist($author);
        $this->em->flush();

        return $this->json([
            'data' => $author
        ]);
    }

    /**
     * @Route("/api/author/{id}", name="users_get_one", methods={"GET"})
     */
    public function getOneAuthor(UserRepository $userRepository, int $id)
    {
        $author = $userRepository->find($id);

        return $this->json([
            'data' => [
                'first_name' => $author->getFirstName(),
                'last_name' => $author->getLastName(),
                'email' => $author->getEmail(),
                'id' => $author->getId(),
                'roles' => $author->getRoles()
            ],
        ]);
    }

    /**
     * @Route("/api/author/{id}", name="users_edit", methods={"PUT"})
     */
    public function editAuthor(Request $request, UserRepository $userRepository, int $id)
    {
        $data = json_decode($request->getContent());

        $author = $userRepository->find($id);

        $author->setFirstName($data->first_name);
        $author->setLastName($data->last_name);
        $author->setEmail($data->email);
        $author->setRoles([]);
        $author->setPassword($this->passwordEncoder->encodePassword($author, $data->password));

        $this->em->persist($author);
        $this->em->flush();

        return $this->json([
            'data' => $author,
        ]);
    }

    /**
     * @Route("/api/author/{id}", name="users_delete", methods={"DELETE"})
     * @param UserRepository $userRepository
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAuthor(UserRepository $userRepository, int $id)
    {
        $user = $userRepository->find($id);
        $this->em->remove($user);
        $this->em->flush();

        return $this->json([
            'data' => 'Author deleted',
        ]);
    }
}
