<?php

declare(strict_types=1);


use App\Domain\ConferenceService;
use App\Domain\ConferenceAggregate;
use App\Domain\ConferenceCommand;
use App\Domain\ConferenceEvent;
use App\EventStore;
use PHPUnit\Framework\TestCase;

final class ConferenceTest extends TestCase
{
    public function test_conference_should_not_be_started(): void
    {
        $service = new ConferenceService($this->createEventStore());
        self::assertFalse($service->started('tdd'));

    }

    public function test_conference_should_start(): void
    {
        $tddConference = new ConferenceService($this->createEventStore());
        $tddConference->start('tdd');

        $tddConference = ConferenceService::load(name: 'tdd', eventStore: $this->createEventStore());
        self::assertTrue($tddConference->isStarted());
    }

    public function test_conference_aggregate_should_not_be_started_when_no_history(): void
    {
        $conferenceAggregate = new ConferenceAggregate([]);
        self::assertFalse($conferenceAggregate->isStarted());
    }

    public function test_conference_aggregate_should_start(): void
    {
        $conferenceAggregate = new ConferenceAggregate([]);
        $event = $conferenceAggregate->handle(ConferenceCommand::START);
        self::assertEquals(ConferenceEvent::STARTED, $event);
    }

    public function test_conference_aggregate_started_should_be_start(): void
    {
        $conferenceAggregate = new ConferenceAggregate([ConferenceEvent::STARTED]);
        self::assertTrue($conferenceAggregate->isStarted());
    }

    public function test_conference_aggregate_should_not_be_start_twice(): void
    {
        $conferenceAggregate = new ConferenceAggregate([ConferenceEvent::STARTED]);
        $event = $conferenceAggregate->handle(ConferenceCommand::START);
        self::assertNull($event);
    }

//    public function test_conference_aggregate_should_not_be_start_twice(): void
//    {
//        $conferenceAggregate = new ConferenceAggregate([ConferenceEvent::STARTED]);
//        $event = $conferenceAggregate->handle(ConferenceCommand::START);
//        self::assertNull($event);
//    }

    private function createEventStore(): EventStore
    {
        return new EventStore(new \App\InMemoryStorage());
    }
}