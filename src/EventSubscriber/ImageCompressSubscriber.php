<?php

namespace App\EventSubscriber;

use App\Event\ImageCompressEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ImageCompressSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            ImageCompressEvent::class => 'onCompressAction',
        ];
    }

    public function onCompressAction(Event $event): void
    {
    }
}
