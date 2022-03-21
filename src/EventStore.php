<?php

declare(strict_types=1);

namespace App;

class EventStore
{
    public function __construct(
        private Storage $storage,
    )
    {
    }

    /**
     * @throws VersionConflict
     * @throws InconsistentVersion
     */
    public function appendEvent(AggregateId $aggregateId, Event $event): void
    {
        $currentVersion = $this->getCurrentVersion($aggregateId);

        if($currentVersion->isGreaterThanOrEquals($event->versionForTest)) {
            throw new VersionConflict();
        }

        if(!$currentVersion->next()->equals($event->versionForTest)) {
            throw new InconsistentVersion();
        }

        $this->storage->store($aggregateId, $event);
    }

    private function getCurrentVersion(AggregateId $aggregateId): Version
    {
        $events = $this->storage->list($aggregateId);

        /** @var Event|false $last */
        $last = end($events);
        return $last !== false ? $last->versionForTest : Version::initial();
    }

    public function list(AggregateId $aggregateId): array
    {
        return $this->storage->list($aggregateId);
    }
}