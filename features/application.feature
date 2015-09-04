Feature: Application
  In order to check if the application is handling requests properly
  As a developer
  I need to be able to assert that all kind of requests are being handled by the application

  Scenario: Handling valid request
    Given I have created a droplet application
    And I have the following routes registered:
      | name | method | path | controller                   |
      | home | get    | /    | DummyController::indexAction |
    When I send a get request to "/"
    Then The response status code should be 200
    And The response should contains "Hello Tales"

  Scenario: Handling non existing route
    Given I have created a droplet application
    When I send a get request to "/some-unknown-route"
    Then The response status code should be 404