<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class EventController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    #[Route('/events', name: 'events')]
    public function index(
        Environment $twig,
        EventRepository $eventRepository
    ): Response {
        return new Response(
            $twig->render(
                'event/index.html.twig',
                [
                    'events' => $eventRepository->findAll(),
                ]
            )
        );
    }


    #[Route('/events/{id}', name: 'event')]
    public function show(
        Environment $twig,
        Event $event,
        CommentRepository $commentRepository,
        Request $request,
        Security $security,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        $isAuthorization = $authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY');
        if ($form->isSubmitted() && $form->isValid() && $isAuthorization) {
            $user = $security->getUser();
            $comment->setAuthor($user);
            $comment->setEvent($event);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            $context = [
                'email' => $event->getAuthor()->getEmail(),
                'event_id' => $event->getId(),
                'event_name' => $event->getName(),
                'comment_email' => $user->getEmail(),
                'comment' => $comment->getComment(),
                'comment_id' => $comment->getId()
            ];
            $this->bus->dispatch(new CommentMessage($comment->getId(), $context));
            return $this->redirectToRoute('event', ['id' => $event->getId()]);
        }

        return new Response(
            $twig->render(
                'event/show.html.twig',
                [
                    'event' => $event,
                    'comments' => $commentRepository->findBy(['event' => $event], ['id' => 'DESC']),
                    'comment_form' => $form->createView(),
                ]
            )
        );
    }
}
