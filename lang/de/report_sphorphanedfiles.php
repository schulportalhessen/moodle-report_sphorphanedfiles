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

$string['pluginname'] = 'Verwaiste Dateien';
$string['deleteMessage'] = 'Verwaiste Datei gelöscht';
$string['header.modName'] = 'Modul';
$string['header.content'] = 'Inhalt';
$string['header.filename'] = 'in diesem Modul verwaiste Datei';
$string['header.preview'] = '';
$string['header.tool'] = 'verwaiste ohne Rückfrage löschen';
$string['isallowedtodeleteallfiles'] = 'Dieser Account darf alle verwaisten Dateien löschen.';
$string['description'] = 'Wenn eine Lehrende Person in einem Editor eine Datei hinzufügt und 
    dann aber wieder löscht, so bleibt diese Datei im Hintergrund noch im Textfeld gespeichert.
    Damit entstehen unterscheidliche Probleme, z.B. kann es sich um eine kopierrechtich geschützte Datei handeln, die
    dann aber bei der Weitergabe von Kursinhalten an andere Lehrende mit weitergegeben werden würden.
    Ebenfalls wird unnötiger Speicherplatz belegt. Daher sollten diese Dateien von den kursverantwortlichen Lehrenden gelöscht werden.
    Dieser Report ermöglicht das Auffinden solcher verwaisten Dateien innerhalb des Kurses.';
$string['header.moduleContent'] = 'Beschreibung, ...';   
$string['header.code'] = 'Quelltext:'; 

$string['sphorphanedfiles:view'] = 'Berechtigung zum Anzeigen des Menüeintrags zum Report.'; 

$string['isactive'] = 'Bericht aktivieren';
$string['configisactive'] = 'Wenn aktiviert kann der Bericht bei vorhandenen Berechtigungen im der Kursnavigation aufgerufen werden.';