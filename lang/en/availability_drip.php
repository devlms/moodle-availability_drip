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

/**
 * Language strings.
 *
 * @package    availability_drip
 * @copyright  2026 DevLMS
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['ajaxerror'] = 'Error contacting the server';
$string['description'] = 'Open this for each learner a number of days after they enrolled in the course.';
$string['error_days'] = 'Enter a whole number of days from 0 to 3650.';
$string['full'] = 'Available {$a->days} days after you enrolled (from {$a->date})';
$string['label_days'] = 'Days after enrolment';
$string['notfull'] = 'Not available {$a->days} days after you enrolled (until {$a->date})';
$string['pluginname'] = 'Restriction by days since enrolment';
$string['privacy:metadata'] = 'The days since enrolment restriction stores no personal data. It reads the enrolment start date.';
$string['short'] = 'Available {$a} days after the learner enrolled';
$string['short_not'] = 'Not available once {$a} days have passed since the learner enrolled';
$string['title'] = 'Days since enrolment';
$string['unit_days'] = 'days after enrolment';
