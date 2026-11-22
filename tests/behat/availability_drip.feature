@availability @availability_drip @javascript
Feature: Days since enrolment restriction
  In order to release content step by step
  As a teacher
  I need to open an activity a number of days after each learner enrolled

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name   |
      | page     | C1     | Week 2 |

  Scenario: Teacher adds the restriction and a new learner sees when it opens
    Given I am on the "Week 2" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I press "Add restriction..."
    And I click on "Days since enrolment" "button" in the "Add restriction..." "dialogue"
    And I set the field "Days after enrolment" to "7"
    And I press "Save and return to course"
    And I log out
    When I am on the "Course 1" "course" page logged in as "student1"
    Then I should see "Available 7 days after you enrolled"
