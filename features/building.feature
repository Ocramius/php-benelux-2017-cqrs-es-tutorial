Feature: Building check in and check out

  Scenario: a user can check into a building
    Given i have a building
    When I check a user into the building
    Then a user should have checked into the building

  Scenario: a user can check out of a building
    Given i have a building
    And a user has checked into the building
    When I check the user out of the building
    Then a user should have checked out of the building

  Scenario: a user cannot check in twice
    Given i have a building
    And a user has checked into the building
    Then the user should not be able to check into the building

  Scenario: a user cannot check out if not checked in first
    Given i have a building
    Then the user should not be able to check out of the building
