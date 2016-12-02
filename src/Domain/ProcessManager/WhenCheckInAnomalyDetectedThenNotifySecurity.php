<?php

declare(strict_types=1);

namespace Building\Domain\ProcessManager;

use Building\Domain\Command\NotifySecurityOfCheckInAnomaly;
use Building\Domain\DomainEvent\CheckInAnomalyDetected;

final class WhenCheckInAnomalyDetectedThenNotifySecurity
{
    /**
     * @var callable
     */
    private $commandBus;

    public function __construct(callable $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(CheckInAnomalyDetected $anomalyDetected)
    {
        ($this->commandBus)(NotifySecurityOfCheckInAnomaly::fromBuildingIdName(
            $anomalyDetected->buildingId(),
            $anomalyDetected->username()
        ));
    }
}
