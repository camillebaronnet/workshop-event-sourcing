<?php

declare(strict_types=1);

namespace App\Domain;

use App\AggregateId;
use App\Event;
use App\EventStore;

final class ConferenceService
{
    public function __construct(private EventStore $eventStore)
    {
    }

    public function start(string $name)
    {
        $aggregate = $this->load($name);
        $event = $aggregate->handle(ConferenceCommand::START);
        $this->eventStore->appendEvent(new Event(Version::initial(), $event));
    }

    public function started(string $name): bool
    {
        $aggregate = $this->load($name);
        return $aggregate->isStarted();
    }

    private function load(string $name)
    {
        $events = $this->eventStore->list(new AggregateId($name));
        return new ConferenceAggregate($events);
    }
}