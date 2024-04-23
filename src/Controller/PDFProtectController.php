<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PDFProtectController extends AbstractController
{
    #[Route('/pdf/protect', name: 'pdf_protect')]
    public function index(): Response
    {
        return $this->render('pdf_protect/index.html.twig', [
            'controller_name' => 'PDFProtectController',
        ]);
    }
}
