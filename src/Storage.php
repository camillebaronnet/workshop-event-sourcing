<?php

declare(strict_types=1);

namespace App;

interface Storage
{
    public function store(AggregateId $aggregateId, Event $event): void;

    public function list(AggregateId $aggregateId): array;
}
