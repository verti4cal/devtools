<?php

namespace App\MessageHandler;

use App\Entity\Image;
use App\Message\ImageCompressMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Imagine\Imagick\Imagine;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsMessageHandler]
class ImageCompressHandler
{
    private EntityManagerInterface $entityManager;
    private Imagine $imagine;
    private string $imageDirectory;

    public function __construct(EntityManagerInterface $entityManager, #[Autowire(param: 'image_directory')] $imageDirectory)
    {
        $this->entityManager = $entityManager;
        $this->imagine = new Imagine();
        $this->imageDirectory = $imageDirectory;
    }

    public function __invoke(ImageCompressMessage $message)
    {
        /** @var Image $image */
        $image = $this->entityManager->getRepository(Image::class)->find($message->getImageId());
        if (empty($image)) {
            return false;
        }

        $compressedFileName = $image->getSafeFileName() . '-compressed';
        $image->setCompressedFileName($compressedFileName);
        $image->setCompressedExtension('png');

        $photo = $this->imagine->open($this->imageDirectory . '/' . $image->getFullFileName());
        $photo->save($this->imageDirectory . '/' . $image->getFullCompressedFileName(), ['png_compression_level' => 7]);

        $image->setCompressedSize(filesize($this->imageDirectory . '/' . $image->getFullCompressedFileName()));

        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }
}
