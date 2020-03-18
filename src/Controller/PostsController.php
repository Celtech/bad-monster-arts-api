<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    private $em;

    /**
     * PostsController constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    /**
     * @Route("/api/posts", name="posts", methods={"GET"})
     */
    public function getPosts(Request $request, PostsRepository $postsRepository)
    {
        $page = $request->get('page') ?? 1;

        $posts = $postsRepository->findAll();
        $length = count($posts);

        $jsonPosts = [];
        for ($i = ($page - 1) * 5; $i < ($page - 1) * 5 + 5; $i++) {
            if(key_exists($i, $posts)) {
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

    /**
     * @Route("/api/posts/{id}", name="posts_one", methods={"GET"})
     */
    public function getOnePost(PostsRepository $postsRepository, int $id)
    {
        $post = $postsRepository->find($id);
        $jsonPosts = $this->toJson($post);

        return $this->json([
            'data' => $jsonPosts,
        ]);
    }

    /**
     * @Route("/api/posts/", name="posts_make", methods={"POST"})
     */
    public function makePost(Request $request, UserRepository $userRepository)
    {
        $data = json_decode($request->getContent());
        $post = new Posts();
        $post->setAuthor($userRepository->find($data->author));
        $post->setTitle($data->title);
        $post->setBody($data->body);
        $post->setTitleImage($data->title_image);
        $post->setCreatedAt(new \DateTime());

        $this->em->persist($post);
        $this->em->flush();
        $jsonPosts = $this->toJson($post);

        return $this->json([
            'data' => $jsonPosts,
        ]);
    }

    /**
     * @Route("/api/posts/{id}", name="posts_edit", methods={"PUT"})
     */
    public function editPost(Request $request, PostsRepository $postsRepository, UserRepository $userRepository, int $id)
    {
        $data = json_decode($request->getContent());

        $post = $postsRepository->find($id);

        $post->setAuthor($userRepository->find($data->author));
        $post->setTitle($data->title);
        $post->setBody($data->body);
        $post->setTitleImage($data->title_image);

        $this->em->persist($post);
        $this->em->flush();

        $jsonPosts = $this->toJson($post);

        return $this->json([
            'data' => $jsonPosts,
        ]);
    }

    /**
     * @Route("/api/posts/{id}", name="posts_delete", methods={"DELETE"})
     */
    public function deletePost(PostsRepository $postsRepository, int $id)
    {
        $post = $postsRepository->find($id);
        $this->em->remove($post);
        $this->em->flush();

        return $this->json([
            'data' => 'Post deleted',
        ]);
    }

    private function toJson(Posts $post, $long = true) {
        $allowedTags = '<br><h3><h4><h5><h6><p><b><i><strong><a><em>';
        $short = $this->truncate(strip_tags($post->getBody(), $allowedTags), 750);

        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'title_image' => $post->getTitleImage() ? $post->getTitleImage() : '',
            'body' => $long ? $post->getBody() : $short,
            'author' => sprintf('%s %s', $post->getAuthor()->getFirstName(), $post->getAuthor()->getLastName()),
            'categories' => $this->categoriesToString($post->getCategories()),
            'created_at' => date_format($post->getCreatedAt(), 'F d, Y'),
        ];
    }

    private function truncate($text, $length, $suffix = '&hellip;', $isHTML = true) {
        $i = 0;
        $simpleTags=array('br'=>true,'hr'=>true,'input'=>true,'image'=>true,'link'=>true,'meta'=>true);
        $tags = array();
        if($isHTML){
            preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            foreach($m as $o){
                if($o[0][1] - $i >= $length)
                    break;
                $t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
                // test if the tag is unpaired, then we mustn't save them
                if($t[0] != '/' && (!isset($simpleTags[$t])))
                    $tags[] = $t;
                elseif(end($tags) == substr($t, 1))
                    array_pop($tags);
                $i += $o[1][1] - $o[0][1];
            }
        }

        // output without closing tags
        $output = substr($text, 0, $length = min(strlen($text),  $length + $i));
        // closing tags
        $output2 = (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');

        $temp = preg_split('/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE);
        $temp1 = end($temp);
        // Find last space or HTML tag (solving problem with last space in HTML tag eg. <span class="new">)
        $pos = (int)end($temp1);
        // Append closing tags to output
        $output.=$output2;

        // Get everything until last space
        $one = substr($output, 0, $pos);
        // Get the rest
        $two = substr($output, $pos, (strlen($output) - $pos));
        // Extract all tags from the last bit
        preg_match_all('/<(.*?)>/s', $two, $tags);
        // Add suffix if needed

        $one = rtrim($one);
        if (strlen($text) > $length) { $one .= $suffix; }
        // Re-attach tags
        $output = $one . implode($tags[0]);

        //added to remove  unnecessary closure
        $output = str_replace('</!-->','',$output);

        return $output;
    }

    private function categoriesToString($categories) {
        $string = '';
        $length = count($categories);

        if($length === 0) {
            return 'Uncategorized';
        }

        for($i = 0; $i < $length; $i++) {
            $string .= $categories[$i]->getName();

            if($i !== ($length - 1)) {
                $string .= ', ';
            }
        }

        return $string;
    }
}
