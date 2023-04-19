<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // Si le user est déjà connecté
        if ($this->getUser()){
            return $this->redirectToRoute('app_contact');
        }
        return $this->redirectToRoute('app_login');
    }
}
