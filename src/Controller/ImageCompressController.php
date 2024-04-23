<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageCompressFormType;
use App\Message\ImageCompressMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\Turbo\TurboBundle;

class ImageCompressController extends AbstractController
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    #[Route('/image/compress', name: 'image_compress')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageCompressFormType::class, $image, [
            'action' => $this->generateUrl('image_compress'),
        ]);
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

            $message = new ImageCompressMessage($image->getId());
            $envelope = $this->bus->dispatch($message);

            $handledStamp = $envelope->last(HandledStamp::class);
            $handledStamp->getResult();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('image_compress/success.stream.html.twig', ['image' => $image]);
            }

            return $this->redirectToRoute('image_compress_success', ['imageId' => $image->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('image_compress/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/image/compress/{imageId}', name: 'image_compress_success')]
    public function success(int $imageId, EntityManagerInterface $entityManager): Response
    {
        $image = $entityManager->getRepository(Image::class)->find($imageId);
        return $this->render('image_compress/success.html.twig', [
            'image' => $image
        ]);
    }
}
