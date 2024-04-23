<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ImageCompressEvent extends Event
{
    public function __construct()
    {
    }

    public function getImage(): void
    {
    }
}
