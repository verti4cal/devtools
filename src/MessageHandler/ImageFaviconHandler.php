<?php

namespace App\MessageHandler;

use App\Entity\Favicon;
use App\Entity\Image;
use App\Message\ImageCompressMessage;
use App\Message\ImageFaviconMessage;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Image\Box;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Imagine\Imagick\Imagine;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsMessageHandler]
class ImageFaviconHandler
{
    private EntityManagerInterface $entityManager;
    private Imagine $imagine;
    private string $imageDirectory;

    private array $faviconSizes = [
        '16x16',
        '32x32',
        '48x48',
        '64x64',
        '128x128',
        '167x167',
        '180x180',
        '192x192',
        '256x256',
        '512x512',
    ];

    public function __construct(EntityManagerInterface $entityManager, #[Autowire(param: 'image_directory')] $imageDirectory)
    {
        $this->entityManager = $entityManager;
        $this->imagine = new Imagine();
        $this->imageDirectory = $imageDirectory;
    }

    public function __invoke(ImageFaviconMessage $message)
    {
        /** @var Image $image */
        $image = $this->entityManager->getRepository(Image::class)->find($message->getImageId());
        if (empty($image)) {
            return false;
        }

        foreach ($this->faviconSizes as $size) {
            $favicon = new Favicon();
            $faviconFileName = $image->getSafeFileName() . '-' . $size;

            $photo = $this->imagine->open($this->imageDirectory . '/' . $image->getFullFileName());
            $splitedSize = explode('x', $size);
            $photo->resize(new Box($splitedSize[0], $splitedSize[1]))->save($this->imageDirectory . '/' . $faviconFileName . '.png', ['png_compression_level' => 7]);

            $favicon->setSize($size);
            $favicon->setSafeFileName($faviconFileName);
            $favicon->setExtension('png');
            $favicon->setImage($image);

            $this->entityManager->persist($favicon);

            $image->addFavicon($favicon);
        }

        // create zip file with all favicons
        $zip = new \ZipArchive();
        $zipFileName = $image->getSafeFileName() . '-favicons.zip';
        $zip->open($this->imageDirectory . '/' . $zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($image->getFavicons() as $favicon) {
            $zip->addFile($this->imageDirectory . '/' . $favicon->getFullFileName(), $favicon->getFullFileName());
        }
        $zip->close();

        $image->setHasFavicon(true);

        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }
}
