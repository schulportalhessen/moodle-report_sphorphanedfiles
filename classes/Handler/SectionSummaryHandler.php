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
        $courseContextId,
        $sectionInfo,
        $user,
        $courseId,
        $globalCfg,
        $iconHtml
    ): array {
        $sectionHtml = $sectionInfo->summary;
        $fileItemIdSectionInfo = $sectionInfo->id;


        // FIXME: Refactor

        $allowedToViewDeleteAllFiles = $this->apiM->security()->allowedToViewDeleteAllFiles(
            $courseId,
            $user
        );

        $userAllowedToDelete = false;

        if ($allowedToViewDeleteAllFiles) {
            $files = $this->apiM->database()->dataFiles()->getFilesForSectionSummary(
                $fileItemIdSectionInfo,
                $courseContextId
            );
            $userAllowedToDelete = true;
        } else {
            $userId = $user->id;
            $files = $this->apiM->database()->dataFiles()->getFilesOfUserForSectionSummary(
                $userId,
                $courseContextId,
                $fileItemIdSectionInfo
            );
        }

        $orphanedFiles = $this->apiM->parser()->extractOrphanedFilesFromString($sectionHtml, $files);

        if (!empty($orphanedFiles)) {
            foreach ($orphanedFiles ?? [] as $file) {
                if ($file->filename !== '.') {
                    $formDelete = (new FileInfo())->setFromFile($file);

                    $viewOrphanedFiles[] = [
                        'modName' => 'course',
                        'instanceId' => 'todo',
                        'contextId' => $courseContextId,
                        'filename' => $this->getFileName(new FileInfo($formDelete)),
                        'preview' => $this->getPreviewForFileWithItemId(new FileInfo($formDelete)),
                        'formDelete' => $formDelete->toArray(),
                        'content' => $sectionHtml,
                        'userAllowedToDelete' => $userAllowedToDelete,
                        'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
                    ];
                }
            }
        }

        return $viewOrphanedFiles;
    }
}
