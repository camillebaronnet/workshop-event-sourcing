<?php

declare(strict_types=1);

namespace App;

final class InMemoryStorage implements Storage {
    private array $events = [];

    public function list(AggregateId $aggregateId): array
    {
        return $this->events[$aggregateId->id] ?? [];
    }

    public function store(AggregateId $aggregateId, Event $event): void
    {
        $this->events[$aggregateId->id][] = $event;
    }
}