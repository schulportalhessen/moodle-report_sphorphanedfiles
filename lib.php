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
 * Public API of the sphorphanedfiles report.
 *
 * Defines the APIs used by sphorphanedfiles reports
 *
 * @package    report_sphorphanedfiles
 * @copyright  2022 Schulportal Hessen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * This function extends the navigation with the report items
 *
 * This function only works for teacher. Student do not get menuitem added. Do not remember the reason why.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_sphorphanedfiles_extend_navigation_course($navigation, $course, $context) {
    // Only show node if report is activated AND user has capability OR report is
    $isactive = get_config('report_sphorphanedfiles', 'isactive');
    $isactiveforadmin = get_config('report_sphorphanedfiles', 'isactiveforadmin');
    $isReportActiveForTheUser = ($isactive || ($isactiveforadmin && is_siteadmin()));
    if ($isReportActiveForTheUser
        && has_capability('moodle/course:manageactivities', $context)
        && has_capability('report/sphorphanedfiles:view', $context)) {

        $page = $GLOBALS['PAGE'];
        $url = new moodle_url('/report/sphorphanedfiles/index.php', array('id' => $course->id));
        $orphanedNode = $page->navigation->find($course->id, navigation_node::TYPE_COURSE);
        $collection = $orphanedNode->children;
        foreach ($collection->getIterator() as $child) {
            $key = $child->key;
            // Add break-condition in order to add menuitem
            // if ($key = 'here the name of the node where to add the new menueentry could be added') {
            //     break;
            // }
            break;
        }
        $node = $orphanedNode->create(get_string('pluginname', 'report_sphorphanedfiles'), $url, navigation_node::NODETYPE_LEAF, null, 'gradebook', new pix_icon('i/report', 'grades'));
        $orphanedNode->add_node($node, $key);
    }
}
