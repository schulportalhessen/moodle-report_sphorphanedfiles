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

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

defined('MOODLE_INTERNAL') || die();

/**
 * Class PageHandler
 * @package report_sphorphanedfiles\Handler
 */
class PageHandler extends ItemHandler
{
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

        // Sonderfall, weil PAge auch HTML-Content hat
        $page = $this->getManager()->database()->dataFiles()->getPage($instance);
        $htmlContent .= '<h4>Seiteninhalt</h4>'
            . file_rewrite_pluginfile_urls(
                $page->content,
                'pluginfile.php',
                $contextId,
                'mod_page',
                'content',
                $page->revision
            );

        $userAllowedToDeleteThisFile = $this->apiM->security()->isUserAllowedToDeleteFiles($courseId, $user);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $htmlContent, $modName);

        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);
            // Bad workaround
            $this->setImplementationmode('xxxxxx');
            if ($file->filearea == 'content') {
                $this->setImplementationmode('item');
            }

            $viewOrphanedFiles[] = $this->getSkeleton(
                $formDelete,
                $file,
                $instance,
                [
                    'modName' => $modName,
                    'name' => $name . " id=" . $instance->id,
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
