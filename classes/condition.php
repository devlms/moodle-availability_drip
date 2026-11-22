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

use core_availability\info;

/**
 * Open an activity or section a number of days after the learner enrolled.
 *
 * The start is the earliest start of the learner's active enrolments in the course
 * (timestart, or timecreated when no start was set), so a second enrolment method never resets it.
 *
 * @package    availability_drip
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var int Largest number of days allowed (ten years). */
    const MAX_DAYS = 3650;

    /** @var int Days after enrolment. */
    protected $days;

    /**
     * Read the saved structure.
     *
     * @param \stdClass $structure {type: 'drip', d: int}
     * @throws \coding_exception when d is missing or out of range
     */
    public function __construct($structure) {
        if (!isset($structure->d) || !is_int($structure->d) || $structure->d < 0 || $structure->d > self::MAX_DAYS) {
            throw new \coding_exception('Missing or invalid ->d for drip condition');
        }
        $this->days = $structure->d;
    }

    /**
     * Structure to save.
     *
     * @return \stdClass
     */
    public function save() {
        return (object)['type' => 'drip', 'd' => $this->days];
    }

    /**
     * JSON for tests and generators.
     *
     * @param int $days
     * @return \stdClass
     */
    public static function get_json(int $days) {
        return (object)['type' => 'drip', 'd' => $days];
    }

    /**
     * When the user's earliest active enrolment in the course started, or null.
     *
     * @param int $courseid
     * @param int $userid
     * @return int|null
     */
    protected static function enrolment_start(int $courseid, int $userid): ?int {
        global $DB;
        $sql = "SELECT MIN(CASE WHEN ue.timestart > 0 THEN ue.timestart ELSE ue.timecreated END)
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE e.courseid = :courseid AND ue.userid = :userid
                       AND e.status = :estatus AND ue.status = :uestatus";
        $start = $DB->get_field_sql($sql, ['courseid' => $courseid, 'userid' => $userid,
            'estatus' => ENROL_INSTANCE_ENABLED, 'uestatus' => ENROL_USER_ACTIVE]);
        return $start ? (int)$start : null;
    }

    /**
     * When it opens for this user, or null when the user is not enrolled.
     *
     * @param info $info
     * @param int $userid
     * @return int|null
     */
    protected function opens_at(info $info, int $userid): ?int {
        $start = self::enrolment_start($info->get_course()->id, $userid);
        return $start === null ? null : $start + $this->days * DAYSECS;
    }

    /**
     * Whether the user may access it.
     *
     * @param bool $not
     * @param info $info
     * @param bool $grabthelot
     * @param int $userid
     * @return bool
     */
    public function is_available($not, info $info, $grabthelot, $userid) {
        $opens = $this->opens_at($info, $userid);
        if ($opens === null) {
            return false;
        }
        $allow = time() >= $opens;
        return $not ? !$allow : $allow;
    }

    /**
     * Text shown to learners and teachers.
     *
     * @param bool $full
     * @param bool $not
     * @param info $info
     * @return string
     */
    public function get_description($full, $not, info $info) {
        global $USER;
        $opens = $this->opens_at($info, $USER->id);
        if ($opens !== null && is_enrolled($info->get_context(), $USER, '', true)) {
            $a = (object)['days' => $this->days, 'date' => userdate($opens, get_string('strftimedatetime', 'langconfig'))];
            return get_string($not ? 'notfull' : 'full', 'availability_drip', $a);
        }
        return get_string($not ? 'short_not' : 'short', 'availability_drip', $this->days);
    }

    /**
     * Debug text for tests.
     *
     * @return string
     */
    protected function get_debug_string() {
        return 'd:' . $this->days;
    }
}
