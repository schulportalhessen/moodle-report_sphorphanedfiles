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
 * @package     report_sph_orphaned_files
 * @category    string
 * @copyright   SPH <andreas.schenkel@schulportal.hessen.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Orphaned files';
$string['deleteMessage'] = 'Orphaned file successfully deleted';
$string['header.modName'] = 'Module';
$string['header.content'] = 'Content';
$string['header.filename'] = 'Filename';
$string['header.preview'] = '';
$string['header.tool'] = 'delete orphanded without request!';
$string['isallowedtodeleteallfiles'] = 'is allowed to delet all files';
$string['description'] = 'This report shows orphaned files that are not used in activitys or resources.';
$string['isgridlayoutfilehint'] = 'This file maybe was used by gridlayout-plugin but seems to be not in use.';

$string['header.moduleContent'] = 'Description, ...';
$string['header.code'] = 'Sourcecode:'; 

$string['sphorphanedfiles:view'] = 'Capability to view menuitem linking to the report.'; 

$string['isactive'] = 'Activate report';
$string['configisactive'] = 'When activate the report can be started in the coursenavigation.';

$string['isactiveforadmin'] = 'Activate report for siteadmin';
$string['configisactiveforadmin'] = 'When activate an admin can start report in the coursenavigation regardless status isactive for normal users.';

$string['handleractivitiescore'] = 'Zu überprüfende Beschreibung bei Core-Aktivitäten';
$string['confighandleractivitiescore'] = 'Nicht alle <b>Core-Aktivitäten</b> verfügen über ein Beschreibungsfeld 8z.B. label). 
    Diese kommaseparierte Liste der Core-Module wird bezüglich der Beschreibung auf verwaiste Dateien geprüft. 
    Gegebenenfalls hier Core-Module ergänzen oder entfernen.';

$string['handleractivitiesplugin'] = 'Zu überprüfende Beschreibung bei Plugin-Aktivitäten';
$string['confighandleractivitiesplugin'] = 'Nicht alle <b>Plugin-Aktivitäten</b> verfügen über ein Beschreibungsfeld. 
    Diese kommasparierte Liste der Module würd bezüglich der Beschreibung auf verwaiste Dateien geprüft. 
    <b>Neue Plugins</b> müssen hier in der Liste ergänzt werden, wenn die Beschreibung unterstützt wird und nach verwaisten Dateien geprüft werden soll.';

$string['handlermaterialscore'] = 'Zu überprüfende Beschreibung bei Core-Materialien';
$string['confighandlermaterialscore'] = 'Nicht alle <b>Core-Materialien</b> verfügen über ein Beschreibungsfeld. 
    Diese kommasparierte Liste der Module würd bezüglich der Beschreibung auf verwaiste Dateien geprüft.
    Gegebenenfalls hier Core-Module ergänzen oder entfernen.';

$string['handlermaterialsplugin'] = 'Zu überprüfende Beschreibung bei Plugin-Materialien';
$string['confighandlermaterialsplugin'] = 'Nicht alle <b>Plugin-Materialien</b> verfügen über ein Beschreibungsfeld. 
    Diese kommasparierte Liste der Module würd bezüglich der Beschreibung auf verwaiste Dateien geprüft.
    <b>Neue Plugins</b> müssen hier in der Liste ergänzt werden, wenn die Beschreibung unterstützt wird und nach verwaisten Dateien geprüft werden soll.';
