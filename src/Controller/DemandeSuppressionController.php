<?php

namespace App\Controller;

use App\Entity\DemandeSuppression;
use App\Form\DemandeSuppressionType;
use App\Repository\DemandeSuppressionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/demande/suppression')]
class DemandeSuppressionController extends AbstractController
{
    #[Route('/', name: 'app_demande_suppression_index', methods: ['GET'])]
    public function index(DemandeSuppressionRepository $demandeSuppressionRepository): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            return $this->render('demande_suppression/index.html.twig', [
                'demande_suppressions' => $demandeSuppressionRepository->findAll(),

            ]);
        }
        return $this->redirectToRoute("app_contact");

    }

    #[Route('/new', name: 'app_demande_suppression_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DemandeSuppressionRepository $demandeSuppressionRepository): Response
    {
        $demandeSuppression = new DemandeSuppression();
        $form = $this->createForm(DemandeSuppressionType::class, $demandeSuppression);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demandeSuppression->setRequestDate(new \DateTime("now"));
            $demandeSuppression->setUserId($this->getUser()->getId());
            $demandeSuppression->setUserDeleted(false);
            $demandeSuppressionRepository->save($demandeSuppression, true);

            return $this->redirectToRoute('app_contact', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande_suppression/new.html.twig', [
            'demande_suppression' => $demandeSuppression,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_suppression_show', methods: ['GET'])]
    public function show(DemandeSuppression $demandeSuppression): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            return $this->render('demande_suppression/show.html.twig', [
                'demande_suppression' => $demandeSuppression,
            ]);
        }

        return $this->redirectToRoute("app_contact");
    }

    #[Route('/{id}/edit', name: 'app_demande_suppression_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DemandeSuppression $demandeSuppression, DemandeSuppressionRepository $demandeSuppressionRepository): Response
    {

        if (in_array('admin', $this->getUser()->getRoles())) {
            $form = $this->createForm(DemandeSuppressionType::class, $demandeSuppression);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $demandeSuppressionRepository->save($demandeSuppression, true);

                return $this->redirectToRoute('app_demande_suppression_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('demande_suppression/edit.html.twig', [
                'demande_suppression' => $demandeSuppression,
                'form' => $form,
            ]);
        }

        return $this->redirectToRoute("app_contact");

    }

    #[Route('/{id}', name: 'app_demande_suppression_delete', methods: ['POST'])]
    public function delete(Request $request, DemandeSuppression $demandeSuppression, DemandeSuppressionRepository $demandeSuppressionRepository): Response
    {
        if (in_array('admin', $this->getUser()->getRoles())) {
            if ($this->isCsrfTokenValid('delete' . $demandeSuppression->getId(), $request->request->get('_token'))) {
                $demandeSuppressionRepository->remove($demandeSuppression, true);
            }

            return $this->redirectToRoute('app_demande_suppression_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute("app_contact");
    }

}
