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
use report_sphorphanedfiles\HTML;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/*
 *  Preparations for later PHP 8 transition.
 * 
 *  We are currently using PHP 7.x which is not the latest PHP version.
 *  Functionality that is built-in in PHP 8 and might be useful in our
 *  modules is provided.
 * 
 *  The following code is safe to execute in PHP 8 environments as a check is
 *  performed before a „substitute“ is provided.
 * 
 */
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle) {
        return empty($needle) || strpos($haystack, $needle) !== false;
    }
}

/**
 * Class SectionSummaryHandler
 */
class SectionSummaryHandler extends ItemHandler
{
    /**
     * @override
     */
    protected function enumerateFiles($user, $context, $course, $fileItemIdSectionInfo): array {
        $result = $this->apiM->database()->dataFiles()->getFilesForSectionSummary($fileItemIdSectionInfo, $context) ?? [];
        return $this->postFilter($result);
    }

    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $sectionInfo,
        $user,
        $courseId,
        $iconHtml
    ): array {
        $sectionHtml = file_rewrite_pluginfile_urls($sectionInfo->summary, 'pluginfile.php', $contextId, 'course', 'section', $sectionInfo->id);
        $userAllowedToDeleteThisFile = $this->apiM->security()->isUserAllowedToDeleteFiles($courseId, $user);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $sectionHtml, $sectionInfo->id);
        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFile($file);

            $viewOrphanedFiles[] = [
                'modName' => 'course',
                'name' => get_string('summary') . ' ' . get_string('section') . ' ' . $sectionInfo->section,
                'instanceId' => 'todo',
                'contextId' => $contextId,
                'filename' => $this->getFileLink(new FileInfo($formDelete)),
                'isGridlayoutFile' => $this->detectGrid($file),
                'preview' => $this->getPreviewForFile(new FileInfo($formDelete)),
                'content' => $sectionHtml,
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

    /**
     * @param stdClass $file file, that has to be checked if it is a gridformat image
     * @return bool
     */
    private function detectGrid(stdClass $file): bool {
        if (str_contains($file->filename, 'goi_') || '/gridimage/' === $file->filepath) {
            return true;
        }
        return false;
    }

    /**
     * @override
     */
    public function getFileLink(FileInfo $fileInfo) {
        $url = $this->apiM->files()->createURLForFileWithItem($this->apiM->files()->getFileUsingPathnamehash($fileInfo->getPathnamehash()));
        return HTML::createLinkInNewTab($url, $fileInfo->getFileName());
    }

}
