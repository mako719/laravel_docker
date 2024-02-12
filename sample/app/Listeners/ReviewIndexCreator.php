<?php

namespace App\Listeners;

use App\DataProvider\AddReviewIndexProviderInterface;
use App\Events\ReviewRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReviewIndexCreator implements ShouldQueue
{
    use InteractsWithQueue;

    private $provider;

    /**
     * Create the event listener.
     */
    public function __construct(
        AddReviewIndexProviderInterface $provider
    )
    {
        $this->provider = $provider;
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewRegistered $event): void
    {
        $this->provider->add(
            $event->getId(),
            $event->getTitle(),
            $event->getContent(),
            $event->getCreatedAtEpoch(),
            $event->getTags(),
            $event->getUserId()
        );
    }
}
