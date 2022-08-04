<?php

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
    function str_contains(string $haystack, string $needle)
    {
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
    protected function enumerateFiles($user, $context, $course, $fileItemIdSectionInfo): array
    {
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
        $sectionHtml = file_rewrite_pluginfile_urls($sectionInfo->summary, 'pluginfile.php',  $contextId, 'course', 'section', $sectionInfo->id);
        $userAllowedToDeleteThisFile =  $this->apiM->security()->isUserAllowedToDeleteFiles($courseId, $user);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $sectionHtml, $sectionInfo->id);
        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFile($file);

            $viewOrphanedFiles[] = [
                'modName' => 'course',
                'name' => get_string('summary') . ' ' . get_string('section') . ' ' .  $sectionInfo->section,
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
        if (str_contains( $file->filename, 'goi_') || '/gridimage/' === $file->filepath) {
            return true;
        }
        return false;
    }

    /**
     * @override
     */
    public function getFileLink(FileInfo $fileInfo)
    {
                $url = $this->apiM->files()->createURLForFileWithItem($this->apiM->files()->getFileUsingPathnamehash($fileInfo->getPathnamehash()));
                return HTML::createLinkInNewTab($url, $fileInfo->getFileName());  
    }

}
