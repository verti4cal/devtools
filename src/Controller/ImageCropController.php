<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageCropFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\Cropperjs\Factory\CropperInterface;
use Symfony\UX\Cropperjs\Form\CropperType;
use Symfony\UX\Turbo\TurboBundle;

class ImageCropController extends AbstractController
{
    #[Route('/image/crop', name: 'image_crop')]
    public function index(CropperInterface $cropper, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageCropFormType::class, $image, [
            'action' => $this->generateUrl('image_crop'),
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

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $crop = $cropper->createCrop($this->getParameter('image_directory') . '/' . $image->getFullFileName());
                $crop->setCroppedMaxSize(2000, 1500);

                $form = $this->createFormBuilder(['crop' => $crop], ['action' => $this->generateUrl('image_crop_action', ['imageId' => $image->getId()])])
                    ->add('crop', CropperType::class, [
                        'public_url' => '/uploads/images/' . $image->getFullFileName(),
                        'cropper_options' => [
                            'initialAspectRatio' => 2000 / 1500,
                        ],
                    ])
                    ->add('save', SubmitType::class, ['label' => 'Crop image', 'attr' => ['class' => 'btn btn-primary mt-2 float-end']])
                    ->getForm();

                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('image_crop/action.stream.html.twig', ['image' => $image, 'form' => $form]);
            }

            return $this->redirectToRoute('image_crop_action', ['imageId' => $image->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('image_crop/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/image/crop/{imageId}', name: 'image_crop_action')]
    public function action(CropperInterface $cropper, Request $request, int $imageId, EntityManagerInterface $entityManager): Response
    {
        $image = $entityManager->getRepository(Image::class)->find($imageId);
        $crop = $cropper->createCrop($this->getParameter('image_directory') . '/' . $image->getFullFileName());
        $crop->setCroppedMaxSize(2000, 1500);

        $form = $this->createFormBuilder(['crop' => $crop], ['action' => $this->generateUrl('image_crop_action', ['imageId' => $image->getId()])])
            ->add('crop', CropperType::class, [
                'public_url' => '/uploads/images/' . $image->getFullFileName(),
                'cropper_options' => [
                    'initialAspectRatio' => 2000 / 1500,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Crop image', 'attr' => ['class' => 'btn btn-primary mt-2 float-end']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $croppedImage = $crop->getCroppedImage('png');
            $croppedFileName = $image->getSafeFileName() . '-cropped';
            $image->setCroppedFileName($croppedFileName);
            $image->setCroppedExtension('png');
            $croppedFilePath = $this->getParameter('image_directory') . '/' . $image->getFullCroppedFileName();
            file_put_contents($croppedFilePath, $croppedImage);
            $image->setCroppedSize(filesize($croppedFilePath));

            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('image_crop_download', ['imageId' => $image->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('image_crop/action.html.twig', [
            'image' => $image,
            'form' => $form
        ]);
    }

    #[Route('/image/crop/{imageId}/download', name: 'image_crop_download')]
    public function download(int $imageId, EntityManagerInterface $entityManager): Response
    {
        $image = $entityManager->getRepository(Image::class)->find($imageId);

        return $this->render('image_crop/download.html.twig', [
            'image' => $image
        ]);
    }
}
