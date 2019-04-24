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
 *
 * @package    local_coursecompletion
 * @copyright nONE
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

defined('MOODLE_INTERNAL') || die();

$userid = required_param('id', PARAM_INT);
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

admin_externalpage_setup('coursecompletion', '', null, '', array('pagelayout' => 'report'));

// It would be possible to get the user courses in the following way and benefit from the API calls:
// $usercourses = enrol_get_all_users_courses($userid);
// The API includes a way to sort based on the requirements.

// For simplicity, I have chosen to just use a single SQL call,
// although it may duplicate tuples via multiple enrolment methods of the same user if they exist in the database.
// It does have the benefit of not requiring multiple SQL calls though.
// (i.e. a call to the enrol_users_courses function and then further calls for completion).

// I am using a html_writer table structure here as suggested, but this could be improved using mustache templates.
$table = new html_table();
$table->head  = array(
    get_string('coursename', 'local_coursecompletion'),
    get_string('completionstatus', 'local_coursecompletion'),
    get_string('completiontime', 'local_coursecompletion')
);
$table->colclasses = array('leftalign', 'centeralign', 'rightalign');
$table->id = 'courses';
$table->attributes['class'] = 'admintable generaltable';
$table->data  = array();

$completed = get_string('completed', 'completion');
$notcompleted = get_string('notcompleted', 'completion');

// TODO: A join to the user table isn't strictly required here.
$sql = "
    SELECT c.id AS courseid, c.fullname AS coursename, cc.timecompleted AS timecompleted
    FROM {user} u
    INNER JOIN {user_enrolments} ue on ue.userid = u.id
    INNER JOIN {enrol} e on e.id = ue.enrolid
    INNER JOIN {course} c on c.id = e.courseid
    LEFT JOIN {course_completions} cc on cc.course = c.id AND cc.userid = u.id
    WHERE u.id = :userid
    ORDER BY c.fullname ASC
";
$params['userid'] = $userid;

// TODO: Could add paging of results here here.
$courses = $DB->get_records_sql($sql, $params);
foreach ($courses as $course) {
    $courselink = new \moodle_url('/course/view.php', array('id' => $course->courseid));
    $coursename = $course->coursename;

    // TODO: A ternary operator assignment for completion could be fun here, but I like this better.
    if ($course->timecompleted > 0) {
        $completion = $completed;
        $timecompleted = userdate($course->timecompleted, get_string('strftimedatetime', 'langconfig'));
    } else {
        $completion = $notcompleted;
        $timecompleted = $notcompleted;
    }

    $row = new html_table_row(array(html_writer::link($courselink, $coursename), $completion, $timecompleted));
    $table->data[] = $row;
}



echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('courselist', 'local_coursecompletion', fullname($user)));

// TODO: The requested table works well enough with my test data,
// but may be unsuitable for mobile devices on sites with large data sets.
echo html_writer::table($table);
echo $OUTPUT->footer();