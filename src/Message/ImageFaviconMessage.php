<?php

namespace App\Message;

class ImageFaviconMessage
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
