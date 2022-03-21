<?php

declare(strict_types=1);

namespace App;

final class Event
{
    public readonly  Version $versionForTest;

    public function __construct(
        public readonly int $version,
        mixed $data
    )
    {
        $this->versionForTest = Version::forTest($this->version);
    }
}
