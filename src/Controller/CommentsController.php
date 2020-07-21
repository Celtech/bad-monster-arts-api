<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Comments;
use App\Entity\Posts;
use App\Repository\CommentsRepository;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    private $em;

    /**
     * PostsController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @Route("/api/posts", name="posts", methods={"GET"})
     */
    public function getComments(Request $request, CommentsRepository $commentsRepository)
    {
        $page = $request->get('page') ?? 1;

        $posts = $commentsRepository->findAll();
        $length = count($posts);

        $jsonPosts = [];
        for ($i = 0; $i < $length; $i++) {
            if (key_exists($i, $posts)) {
                $jsonPosts[] = $this->toJson($posts[$i], false);
            }
        }

        return $this->json([
            'data' => $jsonPosts,
            'length' => $length,
            'pages' => ceil($length / 5),
            'page' => $page,
        ]);
    }


    private function toJson(Comments $comment, $long = true)
    {
        return [
            'id' => $comment->getId(),
            'author' => $comment->getAuthor()->getId(),
            'comment_body' => $comment->getCommentBody(),
            'comments' => $comment->getComments(),
            'created_at' => date_format($comment->getCreatedAt(), 'F d, Y'),
        ];
    }
}
