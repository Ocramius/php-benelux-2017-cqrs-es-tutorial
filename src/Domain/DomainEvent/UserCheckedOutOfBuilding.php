<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class UserCheckedOutOfBuilding extends AggregateChanged
{
    public static function fromBuildingIdAndUsername(Uuid $buildingId, string $username)
    {
        return self::occur((string) $buildingId, ['username' => $username]);
    }

    public function username() : string
    {
        return $this->payload['username'];
    }
}
