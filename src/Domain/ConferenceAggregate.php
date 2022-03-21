<?php

namespace App\Domain;

use App\Event;

class ConferenceState {
    public function __construct(
        public readonly bool $started,
    )
    {
    }
}

/**
 * @internal
 */
final class ConferenceAggregate
{
    private ConferenceState $state;

    /**
     * @param Event[] $events
     */
    public function __construct(array $events)
    {
        $this->state = array_reduce(
            $events,
            static fn(ConferenceState $state, ConferenceEvent $event) => new ConferenceState(true),
            new ConferenceState(false)
        );
    }

    public function isStarted(): bool
    {
        return $this->state->started;
    }

    public function handle(ConferenceCommand $command): ?ConferenceEvent
    {
        if($this->isStarted()) {
            return null;
        }

        return ConferenceEvent::STARTED;
    }
}