<?php

namespace App\Controller;

use App\Entity\DemandeSuppression;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\DemandeSuppressionRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateur')]
class UtilisateurAdminController extends AbstractController
{
    #[Route('/', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            return $this->render('admin/utilisateur/index.html.twig', [
                'utilisateurs' => $utilisateurRepository->findAll(),
            ]);
        }

        return $this->redirectToRoute("app_contact");
    }

    #[Route('/index', name: 'app_admin', methods: ['GET'])]
    public function indexAdmin(UtilisateurRepository $utilisateurRepository): Response
    {


        if (in_array('admin', $this->getUser()->getRoles())) {
            return $this->render('admin/index_admin.html.twig', [
                'utilisateurs' => $utilisateurRepository->findAll(),
            ]);
        }

        return $this->redirectToRoute("app_contact");
    }

    #[
        Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            $utilisateur = new Utilisateur();
            $form = $this->createForm(UtilisateurType::class, $utilisateur);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $utilisateurRepository->save($utilisateur, true);

                return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('utilisateur/new.html.twig', [
                'utilisateur' => $utilisateur,
                'form' => $form,
            ]);
        }

        return $this->redirectToRoute("app_contact");


    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('admin/utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateurRepository->save($utilisateur, true);

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_utilisateur_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Utilisateur $utilisateur, UtilisateurRepository $utilisateurRepository, DemandeSuppressionRepository $dsr): Response
    {
         if (in_array('admin', $this->getUser()->getRoles())) {
            $suppreId = $request->get("suppreId");
            $utilisateurRepository->remove($utilisateur, true);
            $suppreDemande = $dsr->find((int)$suppreId);
            $suppreDemande->setUserDeleted(true);
            $dsr->save($suppreDemande, true);
        }

        return $this->redirectToRoute('app_demande_suppression_index', [], Response::HTTP_SEE_OTHER);
    }
}
