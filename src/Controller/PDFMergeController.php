<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PDFMergeController extends AbstractController
{
    #[Route('/pdf/merge', name: 'pdf_merge')]
    public function index(): Response
    {
        return $this->render('pdf_merge/index.html.twig', [
            'controller_name' => 'PDFMergeController',
        ]);
    }
}
