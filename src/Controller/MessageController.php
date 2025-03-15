<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\ReplyType;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MessageController extends AbstractController{
    #[Route('/message', name: 'app_message')]
    public function index(): Response
    {
        // dd($this->getUser());

        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    #[Route('/message/send', name: 'send')]
    public function send(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        // Traitement du formulaire
        $form->handleRequest($request);
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // l'auteur du message en fonction de l'utilisateur en session
            $message->setSender($this->getUser());
            // La date et l'heure en fonction de la date et heure au moment de la soumission du formulaire
            $message->setCreatedAt(new \DateTimeImmutable());
            // Préparation à l'envoi du message en BDD
            $entityManager->persist($message);
            // Envoi du message
            $entityManager->flush();
            // Ajout d'un message flash
            $this->addFlash(
                'success',
                'Message envoyé'
            );
            // Redirection vers la page de messagerie
            return $this->redirectToRoute('app_message');
        }
        // Affichage du formulaire
        return $this->render('message/send.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/message/reply/{id}', name: 'reply')]
    public function reply(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        // On récupère l'id du destinataire
        $recipient = $entityManager->getRepository(User::class)->find($request->get('id'));
        // dd($recipient);
        // création du formulaire
        $form = $this->createForm(ReplyType::class, $message);
        // Traitement du formulaire
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //l'auteur du message en fonction de l'utilisateur en session
            $message->setSender($this->getUser());
            //date et une heure en fonction de la date et heure au moment de la soumission du formulaire
            $message->setCreatedAt(new \DateTimeImmutable());
            //$recipient qui est définit au-dessus
            $message->setRecipient($recipient);
            // préparation à l'envoi du message en BDD
            $entityManager->persist($message);
            // envoi du message
            $entityManager->flush();
            // Ajout d'un message flash
            $this->addFlash(
                'success',
                'Message envoyé'
            );
            // Redirection vers la page de messagerie
            return $this->redirectToRoute('app_message');
        }
        // Affichage du formulaire
        return $this->render('message/reply.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/message/received', name: 'received')]
    public function received(): Response
    {
        return $this->render('message/received.html.twig');
    }

    #[Route('/message/read/{id}', name: 'read')]
    public function read(Message $message, EntityManagerInterface $entityManager): Response
    {
        // Recuperation de l'utilisateur en session
        $user = $this->getUser();
        // Mise à jour du statut du message
        $message->setIsRead(true);
        // Préparation à l'envoi du message en BDD
        $entityManager->persist($message);
        // Envoi du message
        $entityManager->flush();

        
        // Vérification : L'utilisateur doit être l'expéditeur ou le destinataire du message
        if ($message->getSender() !== $user && $message->getRecipient() !== $user) {
            // Redirection vers la page de messagerie
            return $this->redirectToRoute('app_message');
        }
        // Affichage du message
        return $this->render('message/read.html.twig', compact('message'));
    }

    #[Route('/message/delete/{id}', name: 'delete')]
    public function delete(Message $message, EntityManagerInterface $entityManager): Response
    {
        // Suppression du message
        $entityManager->remove($message);
        // Enregistrement en BDD
        $entityManager->flush();
        // Redirection vers la page de messagerie
        return $this->redirectToRoute('received');
    }

    #[Route('/message/sent', name: 'sent')]
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig');
    }
}
