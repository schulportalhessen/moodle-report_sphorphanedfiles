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
 * Defines the APIs used by sphorphanedfiles reports
 *
 * @package    report_sphorphanedfiles
 * @copyright  2022 Schulportal Hessen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_sphorphanedfiles\Handler;

use mod_assign\privacy\useridlist;
use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;
use function Symfony\Component\String\u;

defined('MOODLE_INTERNAL') || die();

/**
 * Class LabelHandler 
 */
class LabelHandler extends Handler
{
    /**
     * Get the array with the orphaned files view.
     *
     * @param $viewOrphanedFiles
     * @param $contextId
     * @param $user
     * @param $courseId
     * @param $instance
     * @param $iconHtml
     * @return array
     * @throws \coding_exception
     */
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $instance,
        $iconHtml
    ) : array {
        $htmlContent = $instance->content;
        
        $modName = $instance->modname;

        $userAllowedToDeleteThisFile =  $this->apiM->security()->isUserAllowedToDeleteFiles($courseId, $user);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $htmlContent, $modName);
        // echo "$modName: " .  count($orphanedFiles) . '<br />';
        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);
            $viewOrphanedFiles[] =
                [
                'modName' => $modName,
                'name' => get_string('pluginname', 'mod_label') . ' id=' . $instance->id,
                'instanceId' => $instance->id,
                'contextId' => $contextId,
                'filename' => $this->getFileName(new FileInfo($formDelete)),
                'preview' => $this->getPreviewForFile(new FileInfo($formDelete)),
                'content' => $htmlContent,
                'userAllowedToDeleteThisFile' => $userAllowedToDeleteThisFile,
                'filesize' => Misc::convertByteInMegabyte((int)$file->filesize),

                'post_pathnamehash' => $formDelete->getPathnamehash(),
                'post_contextId' => $formDelete->getContextId(),
                'post_component' => $formDelete->getComponent(),
                'post_filearea' => $formDelete->getFileArea(),
                'post_itemId' => $formDelete->getItemId(),
                'post_filepath' => $formDelete->getFilePath(),
                'post_filename' => $formDelete->getFileName()
                ];
        }

        return $viewOrphanedFiles;
    }
}
