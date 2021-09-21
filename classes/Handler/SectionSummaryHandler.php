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
                    $formDelete = [
                        'filearea' => $file->filearea,
                        'itemId' => $file->itemid,
                        'contextId' => $file->contextid,
                        'filepath' => $file->filepath,
                        'filename' => $file->filename,
                        'component' => $file->component
                    ];

                    $orphanedFile = $this->apiM->files()->getFile($formDelete);

                    // prepare preview if image
                    if ($orphanedFile && $orphanedFile->is_valid_image()) {
                        $preview = $this->apiM->files()->generateViewFileForWithItemId(
                            $orphanedFile,
                            $globalCfg
                        );
                    } else {
                        $preview = $this->apiM->files()->generateFallbackView(
                            $orphanedFile,
                            $globalCfg
                        );
                    }

                    $viewOrphanedFiles[] = [
                        'modName' => 'course',
                        'instanceId' => 'todo',
                        'contextId' => $courseContextId,
                        'filename' => $this->getFileName(new FileInfo($formDelete), $globalCfg),
                        'preview' => $preview,
                        'formDelete' => $formDelete,
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
