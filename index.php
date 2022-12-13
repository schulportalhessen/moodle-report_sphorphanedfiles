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
 * Main page for report
 *
 * @package     report_sphorphanedfiles
 * @copyright   Schulportal Hessen (SPH)
 * @author      Andreas Schenkel <andreas.schenkel@schulportal.hessen.de>
 * @author      Markus Heizenreder-Reitz
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
use report_sphorphanedfiles\View\OrphanedView;

defined('MOODLE_INTERNAL') || die();

// 1. Only show index.php for logged in users
require_login();
// 2. Read the id of the course
$courseId = required_param('id', PARAM_INT);
// 3. Check if course exists
$course = $DB->get_record('course', array('id' => $courseId), '*', MUST_EXIST);
// 4. Create context for the course
$context = context_course::instance($course->id);
// 5. Only go head for loggedin users
require_login($courseId);
//6. Only if a user has the capability to view (and use the report including deletion)
require_capability('moodle/course:manageactivities', $context);
require_capability('report/sphorphanedfiles:view', $context);
//7. Check, if the report is active for the actual user
$isactive = get_config('report_sphorphanedfiles', 'isactive');
$isactiveforadmin = get_config('report_sphorphanedfiles', 'isactiveforadmin');
$isReportActiveForTheUser = ($isactive || ($isactiveforadmin && is_siteadmin()));
if (!$isReportActiveForTheUser){
    $msg = get_string('accessruleviolationmessage', 'report_sphorphanedfiles');
    echo $msg;
    die();
}
// 8. Now show report or delete file
$orphanedViewInstance = new OrphanedView($DB, $courseId, $PAGE, $OUTPUT,$USER);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_capability('report/sphorphanedfiles:delete', $context);
    $orphanedViewInstance->deleteOrphanedFile();
}
$orphanedViewInstance->init();
