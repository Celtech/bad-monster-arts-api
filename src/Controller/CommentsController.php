<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Comments;
use App\Entity\Posts;
use App\Repository\CommentsRepository;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/api/comments", name="comments", methods={"POST"})
     * @param Request $request
     * @param CommentsRepository $commentsRepository
     */
    public function postComment(Request $request, CommentsRepository $commentsRepository, PostsRepository $postsRepository)
    {
        $data = json_decode($request->getContent());

        $parentComment = $commentsRepository->find($data->parent_id ?? 0);
        $post = $postsRepository->find($data->post_id ?? 0);

        $comment = new Comments();
        $comment->setAuthor(null);
        $comment->setCommentBody($data->body);
        $comment->setCreatedAt(new \DateTime());
        $comment->setPost($post);
        $comment->setParent($parentComment);

        $this->em->persist($comment);
        $this->em->flush();

        return new JsonResponse($this->commentsToJson($comment));
    }


    private function commentsToJson(Comments $comment) {

        return [
            'id' => $comment->getId(),
            'body' => $comment->getCommentBody(),
            'post' => $comment->getPost()->getId(),
            'created_at' => $comment->getCreatedAt(),
            'author' => $comment->getAuthor() ? [
                'id' => $comment->getAuthor()->getId(),
                'name' => sprintf('%s %s', $comment->getAuthor()->getFirstName(), $comment->getAuthor()->getLastName()),
            ] : null
        ];
    }
}
