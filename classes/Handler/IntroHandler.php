<?php

namespace report_sphorphanedfiles\Handler;

use stdClass;
use cm_info;
use dml_exception;

use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\Files\FileInfo;

/**
 * Class IntroHandler
 * @package report_sphorphanedfiles\Handler
 */
class IntroHandler extends Handler
{
    /**
     * @var string
     */
    private $componentName;

    /**
     * @return string
     */
    public function getComponentName(): string
    {
        return $this->componentName;
    }

    /**
     * @param array $viewOrphanedFiles
     * @param int $contextId
     * @param stdClass $user
     * @param int $courseId
     * @param stdClass $globalCfg
     * @param cm_info $instance
     * @param cm_info $iconHtml
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

        // FIXME: Das ist nicht die optimale passende Stelle für die Instanzvariablen-
        //        zuweisung.
        $this->componentName = $instance->modname;

        $htmlContent = $this->getIntro($instance);
        $name = $instance->name;

        $allowedToViewDeleteAllFiles = $this->apiM->security()->allowedToViewDeleteAllFiles(
            $courseId,
            $user
        );

        $userAllowedToDelete = false;

        if ($allowedToViewDeleteAllFiles) {
            $files = $this->apiM->database()->dataFiles()->getFilesForComponentIntro(
                $contextId,
                $this->getComponentName()
            );
            $userAllowedToDelete = true;
        } else {
            $userId = $user->id;
            $files = $this->apiM->database()->dataFiles()->getFilesOfUserForComponentIntro(
                $userId,
                $contextId,
                $this->getComponentName()
            );
        }

        $orphanedFiles = $this->apiM->parser()->extractOrphanedFilesFromString(
            $htmlContent,
            $files ?? [],
            $contextId
        );

        if (!empty($orphanedFiles)) {
            foreach ($orphanedFiles ?? [] as $file) {
                if ($file->filename !== '.') {
                    $fileInfo = [
                        'filearea' => $file->filearea,
                        'itemId' => $file->itemid,
                        'contextId' => $contextId,
                        'filepath' => $file->filepath,
                        'filename' => $file->filename,
                        'component' => $file->component
                    ];

                    $preview = $this->getPreviewForFile(new FileInfo($fileInfo), $globalCfg);

                    $filename = $this->getFileName(new FileInfo($fileInfo), $globalCfg);

                    $modurl = $this->getModuleURLForInstance($instance);
                    
                    $formDelete = $fileInfo;
                    $viewOrphanedFiles[] = [
                        'modName' => $this->getComponentName(),
                        'name' => $name,
                        'modurl' => $modurl,
                        'instanceId' => $instance->id,
                        'contextId' => $contextId,
                        'filename' => $filename,
                        'preview' => $preview,
                        'formDelete' => $formDelete,
                        'content' => $htmlContent,
                        'userAllowedToDelete' => $userAllowedToDelete,
                        'iconHtml' => $iconHtml,
                        'filesize' => Misc::convertByteInMegabyte((int)$file->filesize)
                    ];
                }
            }
        }
        return $viewOrphanedFiles;
    }
}
