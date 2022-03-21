<?php

declare(strict_types=1);

use App\AggregateId;
use App\Event;
use App\EventStore;
use App\InconsistentVersion;
use App\InMemoryStorage;
use App\Storage;
use App\VersionConflict;
use PHPUnit\Framework\TestCase;

final class EventSourcingTest extends TestCase
{
    public function test_event_store_can_store_an_event(): void
    {
        $storage = $this->createInMemoryStore();

        $eventStore = new EventStore(storage: $storage);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );

        self::assertEquals(
            [new Event(version: 1, data: 'foo')],
            $storage->list(new AggregateId('the-aggregate-id'))
        );
    }

    public function test_should_fail_when_two_conflicting_versions(): void
    {
        $storage = $this->createInMemoryStore();

        $eventStore = new EventStore(storage: $storage);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );

        $this->expectException(VersionConflict::class);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );
    }

    public function test_should_fail_when_three_conflicting_versions(): void
    {
        $storage = $this->createInMemoryStore();

        $eventStore = new EventStore(storage: $storage);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 2, data: 'foo')
        );

        $this->expectException(VersionConflict::class);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 2, data: 'foo')
        );
    }

    public function test_should_fail_when_unordered_versions(): void
    {
        $storage = $this->createInMemoryStore();

        $eventStore = new EventStore(storage: $storage);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 2, data: 'foo')
        );

        $this->expectException(VersionConflict::class);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );
    }

    public function test_event_store_can_store_same_version_on_two_aggregate(): void
    {
        $storage = $this->createInMemoryStore();

        $eventStore = new EventStore(storage: $storage);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id-1'),
            new Event(version: 1, data: 'foo')
        );

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id-2'),
            new Event(version: 1, data: 'bar')
        );

        self::assertEquals(
            [new Event(version: 1, data: 'foo')],
            $storage->list(new AggregateId('the-aggregate-id-1'))
        );

        self::assertEquals(
            [new Event(version: 1, data: 'bar')],
            $storage->list(new AggregateId('the-aggregate-id-2'))
        );
    }

    public function test_should_fail_when_inconsistent_version(): void
    {
        $storage = $this->createInMemoryStore();

        $eventStore = new EventStore(storage: $storage);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );

        $this->expectException(InconsistentVersion::class);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 3, data: 'foo')
        );
    }

    public function test_event_store_can_retrieve_events(): void
    {
        $storage = $this->createInMemoryStore();

        $eventStore = new EventStore(storage: $storage);

        $eventStore->appendEvent(
            new AggregateId('the-aggregate-id'),
            new Event(version: 1, data: 'foo')
        );

        self::assertEquals(
            [new Event(version: 1, data: 'foo')],
            $eventStore->list(new AggregateId('the-aggregate-id'))
        );
    }

    private function createInMemoryStore(): InMemoryStorage
    {
        return new InMemoryStorage();
    }
}
