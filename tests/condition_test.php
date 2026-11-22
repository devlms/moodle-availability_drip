<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace availability_drip;

/**
 * Tests for the days since enrolment condition.
 *
 * @package    availability_drip
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \availability_drip\condition
 */
final class condition_test extends \advanced_testcase {
    /**
     * Course with a page, and a student enrolled with the given start time.
     *
     * @param int $start enrolment timestart
     * @return array [info, userid]
     */
    private function setup_course(int $start): array {
        $this->resetAfterTest();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $page = $gen->create_module('page', ['course' => $course->id]);
        $user = $gen->create_user();
        $gen->enrol_user($user->id, $course->id, 'student', 'manual', $start);
        $cm = get_fast_modinfo($course)->get_cm($page->cmid);
        return [new \core_availability\info_module($cm), $user->id];
    }

    public function test_opens_after_the_days_have_passed(): void {
        [$info, $userid] = $this->setup_course(time() - 10 * DAYSECS);
        $this->assertTrue((new condition((object)['type' => 'drip', 'd' => 7]))->is_available(false, $info, true, $userid));
        $this->assertFalse((new condition((object)['type' => 'drip', 'd' => 14]))->is_available(false, $info, true, $userid));
    }

    public function test_not_inverts(): void {
        [$info, $userid] = $this->setup_course(time() - 10 * DAYSECS);
        $this->assertFalse((new condition((object)['type' => 'drip', 'd' => 7]))->is_available(true, $info, true, $userid));
    }

    public function test_not_enrolled_is_never_available(): void {
        [$info] = $this->setup_course(time() - 10 * DAYSECS);
        $other = $this->getDataGenerator()->create_user();
        $this->assertFalse((new condition((object)['type' => 'drip', 'd' => 0]))->is_available(false, $info, true, $other->id));
    }

    public function test_earliest_active_enrolment_counts(): void {
        [$info, $userid] = $this->setup_course(time() - 2 * DAYSECS);
        $course = $info->get_course();
        $plugin = enrol_get_plugin('self');
        $instanceid = $plugin->add_instance($course, ['status' => ENROL_INSTANCE_ENABLED]);
        $instance = $GLOBALS['DB']->get_record('enrol', ['id' => $instanceid]);
        $plugin->enrol_user($instance, $userid, null, time() - 30 * DAYSECS);
        $this->assertTrue((new condition((object)['type' => 'drip', 'd' => 20]))->is_available(false, $info, true, $userid));
    }

    public function test_save_round_trip_and_bad_data(): void {
        $this->assertEquals((object)['type' => 'drip', 'd' => 5], (new condition((object)['type' => 'drip', 'd' => 5]))->save());
        $this->expectException(\coding_exception::class);
        new condition((object)['type' => 'drip', 'd' => -1]);
    }
}
