<?php

declare(strict_types=1);

namespace App;

final class Version
{
    private function __construct(
        private readonly int $version
    )
    {
    }

    public static function forTest(int $version): Version
    {
        return new Version($version);
    }

    public static function initial(): Version
    {
        return new Version(0);
    }

    public function next(): Version
    {
        return new Version($this->version + 1);
    }

    public function isGreaterThanOrEquals(Version $other): bool
    {
        return $this->version >= $other->version;
    }

    public function equals(Version $other): bool
    {
        return $this->version === $other->version;
    }
}
