<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

/**
 * Class SectionSummaryHandler 
 */
class SectionSummaryHandler extends Handler
{
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $sectionInfo,
        $user,
        $courseId,
        $iconHtml
    ): array {
        $sectionHtml = $sectionInfo->summary;
        $fileItemIdSectionInfo = $sectionInfo->id;


        // FIXME: Refactor

        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);

        $orphanedFiles = $this->enumerateOrphanedFilesInIntroFromString($user, $contextId, $fileItemIdSectionInfo, $courseId, $sectionHtml);


        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFile($file);

            $viewOrphanedFiles[] = [
                'modName' => 'course',
                'instanceId' => 'todo',
                'contextId' => $contextId,
                'filename' => $this->getFileName(new FileInfo($formDelete)),
                'preview' => $this->getPreviewForFileWithItemId(new FileInfo($formDelete)),
                'formDelete' => $formDelete->toArray(),
                'content' => $sectionHtml,
                'userAllowedToDelete' => $userAllowedToDelete,
                'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
            ];
        }

        return $viewOrphanedFiles;
    }
}
