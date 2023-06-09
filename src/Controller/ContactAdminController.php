<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class ContactAdminController extends AbstractController
{
    #[Route('/', name: 'app_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            return $this->render('admin/contact/index.html.twig', [
                'contacts' => $contactRepository->findAll(),
            ]);
        }

        return $this->redirect("app_contact");
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContactRepository $contactRepository): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $contactRepository->save($contact, true);

                return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('contact/new.html.twig', [
                'contact' => $contact,
                'form' => $form,
            ]);
        }

        return $this->redirect("app_contact");
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            return $this->render('contact/show.html.twig', [
                'contact' => $contact,
            ]);
        }

        return $this->redirect("app_contact");
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            $form = $this->createForm(ContactType::class, $contact);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $contactRepository->save($contact, true);

                return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('contact/edit.html.twig', [
                'contact' => $contact,
                'form' => $form,
            ]);
        }

        return $this->redirect("app_contact");
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, ContactRepository $contactRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $contactRepository->remove($contact, true);
        }
        return $this->redirectToRoute('app_contact', [], Response::HTTP_SEE_OTHER);
    }

}
