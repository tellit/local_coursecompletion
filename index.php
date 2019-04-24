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
 * Course completion progress report
 *
 * @package    local
 * @subpackage coursecompletion
 * @copyright  None
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @throws     coding_exception
 * @throws     moodle_exception
 */


require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

defined('MOODLE_INTERNAL') || die();

// Using require_login function as suggested.
require_login();

// TODO: The report should only be available to site administrators
// Could use capabilities here instead.
// e.g. from the require_login function documentation:
// * Please note that use of proper capabilities is always encouraged,
// * this function is supposed to be used from core or for temporary hacks.

// Could offer error explanation via the error API
// e.g. throw new moodle_exception('somesuch');
// Use is_siteadmin function as suggested.
if (!is_siteadmin()) {
    die();
}

// TODO: This function implicitly calls require_login and checks capabilities, so the above is largely unneccessary.
admin_externalpage_setup(
    'coursecompletion', '', null, '', array('pagelayout' => 'report')
);
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('userlist', 'local_coursecompletion'));

// Use API to retrieve all users.
// TODO: Use inbuilt paging in core calls.
// TODO: Use of the $recordsperpage parameter and the paging bar $OUTPUT->paging_bar would work nicely.
$recordsperpage = 10;
$users = get_users(true, '', false, null, 'firstname ASC', '', '', '', $recordsperpage);

// TODO: Could also use a renderer, classes, and templates to generate output instead of the suggested html_writer libraries.
// TODO: The list only displays the users username, but could be much more sophisticated, showing more detail.
$output = '';
foreach ($users as $user) {
    $userurl = new moodle_url('/local/coursecompletion/user.php', array('id' => $user->id));
    $output .= html_writer::link($userurl, $user->username) . '<br/>';
}

echo $output;

echo $OUTPUT->footer();