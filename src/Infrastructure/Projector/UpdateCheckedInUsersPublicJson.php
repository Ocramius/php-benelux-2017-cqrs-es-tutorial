<?php

declare(strict_types=1);

namespace Building\Infrastructure\Projector;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;
use Building\Domain\DomainEvent\UserCheckedOutOfBuilding;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\StreamName;

final class UpdateCheckedInUsersPublicJson
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function __invoke(AggregateChanged $triggeringEvent)
    {
        $buildings = [];

        $stream = $this->eventStore->loadEventsByMetadataFrom(
            new StreamName('event_stream'),
            ['aggregate_id' => $triggeringEvent->aggregateId()]
        );

        foreach ($stream as $event) {
            if ($event instanceof NewBuildingWasRegistered) {
                $buildings[$event->aggregateId()] = [];
            }

            if ($event instanceof UserCheckedIntoBuilding) {
                $buildings[$event->aggregateId()][$event->username()] = null;
            }

            if ($event instanceof UserCheckedOutOfBuilding) {
                unset($buildings[$event->aggregateId()][$event->username()]);
            }
        }

        foreach (array_map('array_keys', $buildings) as $buildingId => $users) {
            file_put_contents(__DIR__ . '/../../../public/building-' . $buildingId . '.json', json_encode($users));
        }
    }
}
