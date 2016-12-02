<?php

declare(strict_types=1);

namespace BuildingAcceptanceTesting;

use Behat\Behat\Context\Context;
use Building\Domain\Aggregate\Building;

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
    }

    /**
     * @When I check a user into the building
     */
    public function iCheckAUserIntoTheBuilding()
    {
        $this->building->checkInUser('username');
    }

    /**
     * @Then a user has checked into the building
     */
    public function aUserHasCheckedIntoTheBuilding()
    {
        // @todo

        throw new \BadMethodCallException('incomplete');
    }
}
