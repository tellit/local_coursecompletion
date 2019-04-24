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
 * Add page to admin menu.
 *
 * @package    local_coursecompletion
 * @copyright  None
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @throws     \moodle_exception
 * @throws     \coding_exception
 */

defined('MOODLE_INTERNAL') || die;

// TODO: The report should appear as a link in the 'reports' section of the "Site administration" navigation.

if ($hassiteconfig) {
    $ADMIN->add('reports', new admin_externalpage('coursecompletion',
            get_string('pluginname', 'local_coursecompletion'),
            new \moodle_url('/local/coursecompletion/index.php'))
    );

    // No report settings.
    $settings = null;
}
