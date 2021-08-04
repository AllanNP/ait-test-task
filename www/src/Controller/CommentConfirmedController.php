<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentConfirmedController extends AbstractController
{
    #[Route('/comment/confirmed/{id}', name: 'comment_confirmed')]
    public function index(
        Comment $comment,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        $eventAuthor = $comment->getEvent()->getAuthor();
        if ($eventAuthor->getId() != $security->getUser()->getId()) {
            throw $this->createNotFoundException('The comment does not exist');
        }
        if($comment->getConfirmed() === false) {
            $comment->setConfirmed(true);
            $entityManager->persist($comment);
            $entityManager->flush();
        }
        return $this->render(
            'comment_confirmed/index.html.twig',
            [
                'comment' => $comment,
            ]
        );
    }
}
