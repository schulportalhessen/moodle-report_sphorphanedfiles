<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

/**
 * Class LabelHandler 
 */
class LabelHandler extends Handler
{
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $globalCfg,
        $instance,
        $iconHtml
    ): array {
        $htmlContent = $instance->content;
        $modName = $instance->modname;

        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);

        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $modName, $courseId, $htmlContent);

        // FIXME: Refactor

        foreach ($orphanedFiles as $file) {
            $formDelete = [
                'filearea' => $file->filearea,
                'itemId' => $file->itemid,
                'contextId' => $contextId,
                'filepath' => $file->filepath,
                'filename' => $file->filename,
                'component' => $file->component
            ];

            $preview = $this->getPreviewForFile(new FileInfo($formDelete), $globalCfg);

            $filename = $this->getFileName(new FileInfo($formDelete), $globalCfg);

            $viewOrphanedFiles[] = [
                'modName' => $modName,
                'instanceId' => $instance->id,
                'contextId' => $contextId,
                'filename' => $filename,
                'preview' => $preview,
                'formDelete' => $formDelete,
                'content' => $htmlContent,
                'userAllowedToDelete' => $userAllowedToDelete,
                'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
            ];
        }
        return $viewOrphanedFiles;
    }
}
