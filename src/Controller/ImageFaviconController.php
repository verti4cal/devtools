<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageFaviconFormType;
use App\Message\ImageFaviconMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\Turbo\TurboBundle;

class ImageFaviconController extends AbstractController
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    #[Route('/image/favicon', name: 'image_favicon')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageFaviconFormType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename) . '-' . uniqid();
                $image->setSafeFileName($safeFilename);
                $image->setExtension($imageFile->guessExtension());
                $newFilename = $image->getFullFileName();

                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );

                    $image->setSize(filesize($this->getParameter('image_directory') . '/' . $newFilename));
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }

            $entityManager->persist($image);
            $entityManager->flush();

            $message = new ImageFaviconMessage($image->getId());
            $envelope = $this->bus->dispatch($message);

            $handledStamp = $envelope->last(HandledStamp::class);
            $handledStamp->getResult();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('image_favicon/success.stream.html.twig', ['image' => $image]);
            }

            return $this->redirectToRoute('image_favicon_success', ['imageId' => $image->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('image_favicon/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/image/favicon/{imageId}', name: 'image_favicon_success')]
    public function success(int $imageId, EntityManagerInterface $entityManager): Response
    {
        $image = $entityManager->getRepository(Image::class)->find($imageId);
        return $this->render('image_favicon/success.html.twig', [
            'image' => $image
        ]);
    }
}
