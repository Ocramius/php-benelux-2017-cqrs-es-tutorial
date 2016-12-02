<?php

declare(strict_types=1);

namespace BuildingAcceptanceTesting;

use Assert\Assertion;
use Behat\Behat\Context\Context;
use Building\Domain\Aggregate\Building;
use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;
use Prooph\EventSourcing\AggregateChanged;

final class BuildingContext implements Context
{
    /**
     * @var Building
     */
    private $building;

    /**
     * @Given i have a building
     */
    public function iHaveABuilding()
    {
        $this->building = Building::new('foo');

        $this->assertEvents(NewBuildingWasRegistered::occur(
            (string) $this->building->id(),
            ['name' => 'foo']
        ));
    }

    /**
     * @When I check a user into the building
     */
    public function iCheckAUserIntoTheBuilding()
    {
        $this->building->checkInUser('username');
    }

    /**
     * @Then a user should have checked into the building
     */
    public function aUserShouldHaveCheckedIntoTheBuilding()
    {
        $this->assertEvents(UserCheckedIntoBuilding::fromBuildingIdAndUsername(
            $this->building->id(),
            'username'
        ));
    }

    private function assertEvents(AggregateChanged $eventBlueprint)
    {
        $reflectionPopEvents = new \ReflectionMethod($this->building, 'popRecordedEvents');

        $reflectionPopEvents->setAccessible(true);

        $events = $reflectionPopEvents->invoke($this->building);

        Assertion::count($events, 1);
        Assertion::same(get_class($eventBlueprint), get_class($events[0]));
        Assertion::eq($eventBlueprint->payload(), $events[0]->payload());
    }
}
