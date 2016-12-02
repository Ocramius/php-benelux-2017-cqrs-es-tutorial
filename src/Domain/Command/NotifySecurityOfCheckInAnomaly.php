<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

final class NotifySecurityOfCheckInAnomaly extends Command
{
    /**
     * @var Uuid
     */
    private $buildingId;

    /**
     * @var string
     */
    private $username;

    private function __construct(Uuid $buildingId, string $username)
    {
        $this->init();

        $this->buildingId = $buildingId;
        $this->username   = $username;
    }

    public static function fromBuildingIdName(Uuid $buildingId, string $name) : self
    {
        return new self($buildingId, $name);
    }

    public function buildingId() : Uuid
    {
        return $this->buildingId;
    }

    public function username() : string
    {
        return $this->username;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'username'   => $this->username,
            'buildingId' => (string) $this->buildingId,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->username   = $payload['username'];
        $this->buildingId = Uuid::fromString($payload['buildingId']);
    }
}
