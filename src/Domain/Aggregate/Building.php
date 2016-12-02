<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\CheckInAnomalyDetected;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;
use Building\Domain\DomainEvent\UserCheckedOutOfBuilding;
use Building\Domain\Repository\BannedUsersInterface;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var <string, null>array
     */
    private $checkedInUsers = [];

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username, BannedUsersInterface $bannedUsers)
    {
        if ($bannedUsers->isBanned($username)) {
            throw new \OutOfBoundsException(sprintf('User "%s" is banned, and cannot check-in', $username));
        }

        $anomalyDetected = \array_key_exists($username, $this->checkedInUsers);

        $this->recordThat(UserCheckedIntoBuilding::fromBuildingIdAndUsername(
            $this->uuid,
            $username
        ));
        if ($anomalyDetected) {
            $this->recordThat(CheckInAnomalyDetected::fromBuildingIdAndUsername(
                $this->uuid,
                $username
            ));
        }
    }

    public function checkOutUser(string $username)
    {
        $anomalyDetected = ! \array_key_exists($username, $this->checkedInUsers);

        $this->recordThat(UserCheckedOutOfBuilding::fromBuildingIdAndUsername(
            $this->uuid,
            $username
        ));

        if ($anomalyDetected) {
            $this->recordThat(CheckInAnomalyDetected::fromBuildingIdAndUsername(
                $this->uuid,
                $username
            ));
        }
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = Uuid::fromString($event->aggregateId());
        $this->name = $event->name();
    }

    public function whenUserCheckedIntoBuilding(UserCheckedIntoBuilding $event)
    {
        $this->checkedInUsers[$event->username()] = null;
    }

    public function whenUserCheckedOutOfBuilding(UserCheckedOutOfBuilding $event)
    {
        unset($this->checkedInUsers[$event->username()]);
    }

    public function whenCheckInAnomalyDetected(CheckInAnomalyDetected $event)
    {
        // nothing, for now
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : Uuid
    {
        return Uuid::fromString($this->aggregateId());
    }
}
