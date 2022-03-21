<?php

declare(strict_types=1);

namespace App;

final class AggregateId
{
    public function __construct(
        public readonly string $id
    )
    {
    }
}