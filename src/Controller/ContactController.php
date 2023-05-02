<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Repository\DemandeSuppressionRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(DemandeSuppressionRepository $dsr): Response
    {
        $user = $this->getUser();
        $contacts = $user->getContacts();

        $deletionRequest = $dsr->findOneBy(['userId' => $this->getUser()->getId()]);

        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'hasADeletionRequest' => $deletionRequest !== null
        ]);
    }

    #[Route('/contact/add_contact', name: 'app_contact_add')]
    public function add_contact(Request $request, UtilisateurRepository $ur, ContactRepository $cr): Response
    {
        // Stocker le mail envoyé dans le Get
        $email = $request->get('email');
        $message = "";

        // Si un mail a été renseigné
        if ($email) {
            // Recherche l'hypothétique utilisateur associé au mail
            $userFoundByEmail = $ur->findOneBy([
                'email' => $email
            ]);

            /* Si cet utilisateur existe, créer un objet Contact
            *   -
            *
            */
            if ($userFoundByEmail) {
                $contact = new Contact();
                $contact->setIdUtilisateur($this->getUser());
                $contact->setContact($userFoundByEmail);

                $userContacts = $this->getUser()->getContacts();

                $alreadyExist = $userContacts->filter(function ($contactData) use (&$contact) {
                    return $contactData->getContact()->getEmail() === $contact->getContact()->getEmail();
                });

                if (count($alreadyExist) > 0) {
                    $message = "You already added this contact";
                } elseif ($this->getUser() === $contact->getContact()) {
                    $message = "You cannot add yourself";
                } else {
                    $cr->save($contact, true);
                }
            }
        }

        if ($message === "") {
            $message = "If " . $email . " is registered, it has been added.";
        }

        return $this->render('contact/add_contact.html.twig',
            [
                'message' => $message
            ]);
    }
}
