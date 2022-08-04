<?php

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
    public function enumerateOrphanedFilesFromString($user, $contextId, $modName, $courseId, $htmlContent): array
    {
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
