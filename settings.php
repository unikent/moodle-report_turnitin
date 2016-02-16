<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Turnitin report.
 *
 * @package   report_turnitin
 * @copyright 2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

debugging("report_turnitin is deprecated");

$ADMIN->add('reports', new admin_externalpage(
    'reportturnitin',
    get_string('pluginname', 'report_turnitin'),
    new \moodle_url('/report/turnitin/index.php'),
    'report/turnitin:view'
));

// No report settings.
$settings = null;
