<?php

namespace report_sphorphanedfiles\Handler;

use stdClass;
use cm_info;
use dml_exception;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\Misc;

/**
 * Class ChoiceHandler
 * @package report_sphorphanedfiles\Handler
 */
class ChoiceHandler extends Handler
{
    /**
     * @param array $viewOrphanedFiles
     * @param int $contextId
     * @param stdClass $user
     * @param int $courseId
     * @param stdClass $globalCfg
     * @param cm_info $instance
     * @return array
     * @throws dml_exception
     */
    public function getViewOrphanedFiles(
        $viewOrphanedFiles,
        $contextId,
        $user,
        $courseId,
        $globalCfg,
        $instance,
        $iconHtml
    ): array {

        $htmlContent = $this->getIntro($instance);

        $modName = $instance->modname;

        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);

        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $modName, $courseId, $htmlContent);

        foreach ($orphanedFiles as $file) {
            $formDelete = [
                'filearea' => $file->filearea,
                'itemId' => $file->itemid,
                'contextId' => $contextId,
                'filepath' => $file->filepath,
                'filename' => $file->filename,
                'component' => $file->component
            ];

            $viewOrphanedFiles[] = [
                'modName' => $modName,
                'instanceId' => $instance->id,
                'contextId' => $contextId,
                'filename' => $file->filename,
                'preview' => $this->getPreviewForFile(new FileInfo($formDelete), $globalCfg),
                'formDelete' => $formDelete,
                'content' => $htmlContent,
                'userAllowedToDelete' => $userAllowedToDelete,
                'filesize' => Misc::convertByteInMegabyte($file->filesize)
            ];
        }

        return $viewOrphanedFiles;
    }
}
