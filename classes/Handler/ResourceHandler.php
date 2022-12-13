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

namespace report_sphorphanedfiles\Handler;

use cm_info;
use dml_exception;
use stdClass;

use report_sphorphanedfiles\Files\FileInfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class ResourceHandler
 * @package report_sphorphanedfiles\Handler
 */
class ResourceHandler extends Handler
{
    /**
     * @override
     */
    public function enumerateOrphanedFilesFromString($user, $contextId, $modName, $courseId, $htmlContent): array {
        //
        // Unklar:
        // Remove file area content, because content files can´t be orphaned in mod resource
        //
        return array_filter(
            parent::enumerateOrphanedFilesFromString($user, $contextId, $modName, $courseId, $htmlContent),
            function ($file, $key) {
                return $file->filearea === 'intro';
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * @param array $viewOrphanedFiles
     * @param int $contextId
     * @param stdClass $user
     * @param int $courseId
     * @param cm_info $instance
     * @return array
     * @throws dml_exception
     */
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $instance,
        $iconHtml
    ): array {
        $htmlContent = $this->getIntro($instance);

        $modName = $instance->modname;
        $name = $instance->name;

        $userAllowedToDeleteThisFile = $this->apiM->security()->isUserAllowedToDeleteFiles($courseId, $user);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $htmlContent, $modName);

        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);

            $viewOrphanedFiles[] = $this->getSkeleton(
                $formDelete,
                $file,
                $instance,
                [
                    'modName' => $modName,
                    'name' => $name,
                    'instanceId' => $instance->id,
                    'contextId' => $contextId,
                    'content' => $htmlContent,
                    'userAllowedToDeleteThisFile' => $userAllowedToDeleteThisFile,
                    'iconHtml' => $iconHtml,

                    'post_pathnamehash' => $formDelete->getPathnamehash(),
                    'post_contextId' => $formDelete->getContextId(),
                    'post_component' => $formDelete->getComponent(),
                    'post_filearea' => $formDelete->getFileArea(),
                    'post_itemId' => $formDelete->getItemId(),
                    'post_filepath' => $formDelete->getFilePath(),
                    'post_filename' => $formDelete->getFileName()
                ]
            );
        }

        return $viewOrphanedFiles;
    }
}
