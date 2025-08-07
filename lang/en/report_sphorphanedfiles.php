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
 * Plugin strings are defined here.
 *
 * @package     report_sphorphanedfiles
 * @category    string
 * @copyright   SPH <andreas.schenkel@schulportal.hessen.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accessruleviolationmessage'] = 'Report is not activated or missing capability';
$string['confighandleractivitiescore'] = 'Not every <b>moodle core activity</b> supports intro description (eg. label). This comma-separated list of moodle core activity will be checked for orphaned files in intro description. New moodle core activity have to be added to this list.';
$string['confighandleractivitiesplugin'] = 'Not every <b>additional activity</b> supports intro description. This comma-separated list of additional activitys will be checked for orphaned files in intro description. New additional activitys have to be added to this list if they support intro description.';
$string['confighandlermaterialscore'] = 'Not every <b>moodle core resources</b> supports intro description. This comma-separated list of moodle core resources will be checked for orphaned files in intro description. New moodle core resources have to be added to this list if they support intro description.';
$string['confighandlermaterialsplugin'] = 'Not every <b>additional resources</b> supports intro description. This comma-separated list of additional resources will be checked for orphaned files in intro description. New additional resources have to be added to this list if they support intro description.';
$string['configisactive'] = 'When activate the report can be started in the coursenavigation.';
$string['configisactiveforadmin'] = 'When activate an admin can start report in the coursenavigation regardless status isactive for normal users.';
$string['deleteMessage'] = 'Orphaned file successfully deleted';
$string['description'] = 'If a teacher adds a file to the editor and delete the file then the file is still be stored in the background of this context (eg label). The teacher can delete the file with the "manage file" dialog in the editor. This is not very comfortable. This report helps to shows orphaned files that might are not used in descriptions of activitys or resources and helps to delete them.';
$string['handleractivitiescore'] = 'Check this moodle core activitys for orphaned files in intro description';
$string['handleractivitiesplugin'] = 'Check this moodle additional activitys for orphaned files in intro description';
$string['handlermaterialscore'] = 'Check this moodle core resources for orphaned files in intro description';
$string['handlermaterialsplugin'] = 'Check this moodle additional resources for orphaned files in intro description';
$string['header.code'] = 'Sourcecode:';
$string['header.content'] = 'Content';
$string['header.filename'] = 'Filename';
$string['header.modName'] = 'Module';
$string['header.moduleContent'] = 'Description, ...';
$string['header.preview'] = '';
$string['header.tool'] = 'delete orphanded without request!';
$string['invalidcourseidmessage'] = 'invalid courseid';
$string['isactive'] = 'Activate report';
$string['isactiveforadmin'] = 'Activate report for siteadmin';
$string['isallowedtodeleteallfiles'] = 'User is allowed / User has capability to delete all files in this course';
$string['isgridlayoutfilehint'] = 'This file maybe was used by gridlayout-plugin but seems to be not in use.';
$string['pluginname'] = 'Report orphaned files';
$string['sphorphanedfiles:delete'] = 'Capability to get an icon added to each orphaned file to be able to delete files.';
$string['sphorphanedfiles:view'] = 'Capability to view this report.';
