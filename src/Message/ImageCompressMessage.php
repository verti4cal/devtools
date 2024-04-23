<?php

namespace App\Message;

class ImageCompressMessage
{
    public function __construct(
        private string $imageId,
    ) {
    }

    public function getImageId(): int
    {
        return $this->imageId;
    }
}
