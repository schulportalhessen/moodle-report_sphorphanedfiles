<?php

namespace report_sphorphanedfiles\Handler;

use cm_info;
use dml_exception;
use stdClass;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

/**
 * Class ResourceHandler
 * @package report_sphorphanedfiles\Handler
 */
class ResourceHandler extends Handler
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
        $name = $instance->name;

        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);

        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $modName, $courseId, $htmlContent);

        //
        // Unklar:
        // Remove file area content, because content files can´t be orphaned in mod resource
        //
        $orphanedFiles = array_filter($orphanedFiles, function ($file, $key) {
            return $file->filearea === 'intro';
        }, ARRAY_FILTER_USE_BOTH);

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
                'name' => $name,
                'modurl' => $this->getModuleURLForInstance($instance),
                'instanceId' => $instance->id,
                'contextId' => $contextId,
                'filename' => $this->getFileName(new FileInfo($formDelete), $globalCfg),
                'preview' => $this->getPreviewForFile(new FileInfo($formDelete), $globalCfg),
                'formDelete' => $formDelete,
                'content' => $htmlContent,
                'userAllowedToDelete' => $userAllowedToDelete,
                'iconHtml' => $iconHtml,
                'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
            ];
        }

        return $viewOrphanedFiles;
    }
}
