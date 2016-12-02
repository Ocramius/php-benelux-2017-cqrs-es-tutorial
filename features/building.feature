Feature: Building check in and check out

  Scenario: a user can check into a building
    Given i have a building
    When I check a user into the building
    Then a user has checked into the building
