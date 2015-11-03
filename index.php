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

require(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('reportturnitin', '', null, '', array('pagelayout' => 'report'));

$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 30, PARAM_INT);
$sort    = optional_param('sort', 'timemodified', PARAM_ALPHAEXT);
$dir     = optional_param('dir', 'DESC', PARAM_ALPHA) == 'DESC' ? 'DESC' : 'ASC';

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_turnitin'));

$table = new html_table();
$table->data = array();

// Do the table headers.
$cells = array();
$cells["course"] = new html_table_cell(get_string("course"));
$cells["name"] = new html_table_cell(get_string("name"));
$cells["start_date"] = new html_table_cell(get_string("dtstart", "turnitintooltwo"));
$cells["due_date"] = new html_table_cell(get_string("dtdue", "turnitintooltwo"));
$cells["post_date"] = new html_table_cell(get_string("dtpost", "turnitintooltwo"));
$cells["number_of_parts"] = new html_table_cell(get_string("numberofparts", "turnitintooltwo"));
$cells["submissions"] = new html_table_cell(get_string("submissions", "turnitintooltwo"));
$table->head = $cells;

$courses = enrol_get_users_courses($USER->id, false);
foreach ($courses as $course) {
    if (!has_capability('moodle/course:update', \context_course::instance($course->id))) {
        continue;
    }

    $turnitintooltwos = get_all_instances_in_course("turnitintooltwo", $course);
    foreach ($turnitintooltwos as $turnitintooltwo) {
        $cells["course"] = new html_table_cell(\html_writer::link(new \moodle_url('/course/view.php', array(
            'id' => $course->id
        )), $course->shortname));

        $cm = get_coursemodule_from_id('turnitintooltwo', $turnitintooltwo->coursemodule, $course->id);
        $turnitintooltwoassignment = new turnitintooltwo_assignment($turnitintooltwo->id, $turnitintooltwo);

        // Show links dimmed if the mod is hidden.
        $attributes["class"] = (!$turnitintooltwo->visible) ? 'dimmed' : '';
        $linkurl = $CFG->wwwroot.'/mod/turnitintooltwo/view.php?id='.
                        $turnitintooltwoassignment->turnitintooltwo->coursemodule.'&do=submissions';

        $cells["name"] = new html_table_cell(html_writer::link($linkurl, $turnitintooltwo->name, $attributes));

        $records = $DB->get_records('turnitintooltwo_parts', array(
            'turnitintooltwoid' => $turnitintooltwo->id
        ));

        $dates = new \stdClass();
        foreach ($records as $record) {
            $dates->dtstart = (empty($dates->dtstart) ? '' : '<br />') . userdate($record->dtstart, get_string('strftimedatetimeshort', 'langconfig'));
            $dates->dtdue = (empty($dates->dtdue) ? '' : '<br />') . userdate($record->dtdue, get_string('strftimedatetimeshort', 'langconfig'));
            $dates->dtpost = (empty($dates->dtpost) ? '' : '<br />') . userdate($record->dtpost, get_string('strftimedatetimeshort', 'langconfig'));
        }


        $cells["start_date"] = new html_table_cell($dates->dtstart);
        $cells["start_date"]->attributes["class"] = "centered_cell";

        $cells["due_date"] = new html_table_cell($dates->dtdue);
        $cells["due_date"]->attributes["class"] = "centered_cell";

        $cells["post_date"] = new html_table_cell($dates->dtpost);
        $cells["post_date"]->attributes["class"] = "centered_cell";

        $cells["number_of_parts"] = new html_table_cell(count($turnitintooltwoassignment->get_parts()));
        $cells["number_of_parts"]->attributes["class"] = "centered_cell";

        if (has_capability('mod/turnitintooltwo:grade', context_module::instance($cm->id))) {
            $noofsubmissions = $turnitintooltwoassignment->count_submissions($cm, 0);
        } else {
            $noofsubmissions = count($turnitintooltwoassignment->get_user_submissions($USER->id,
                                                    $turnitintooltwoassignment->turnitintooltwo->id));
        }
        $cells["submissions"] = new html_table_cell(html_writer::link($linkurl, $noofsubmissions, $attributes));
        $cells["submissions"]->attributes["class"] = "centered_cell";

        $table->data[] = new html_table_row($cells);
    }
}

echo $OUTPUT->box(html_writer::table($table), 'generalbox boxaligncenter');

echo $OUTPUT->footer();