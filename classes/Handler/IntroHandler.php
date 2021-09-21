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
    private const handlerActivities = [
        'assign',
        'bigbluebuttonbn',
        'checklist',
        'choice',
        'customcert',
        'data',
        'lti',
        'ratingallocate',
        'feedback',
        'forum',
        'geogebra',
        'glossary',
        'h5pactivity',
        'hotpot',
        'hvp',
        'lesson',
        'mootyper',
        'pdfannotator',
        'quiz',
        'realtimequiz',
        'scorm',
        'survey',
        'wiki',
        'workshop',
    ];

    private const handlerMaterials = [
        'book',
        'folder',
        'imscp',
        'lightboxgallery',
        'url',
        'edusharing',
        'unilabel'
    ];

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
     * @override
     */
    public function canHandle(string $component): bool
    {
        if (in_array($component, self::handlerActivities))
            return true;

        if (in_array($component, self::handlerMaterials))
            return true;

        return false;
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
                    $formDelete = [
                        'filearea' => $file->filearea,
                        'itemId' => $file->itemid,
                        'contextId' => $contextId,
                        'filepath' => $file->filepath,
                        'filename' => $file->filename,
                        'component' => $file->component
                    ];

                    $viewOrphanedFiles[] = [
                        'modName' => $this->getComponentName(),
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
            }
        }
        
        return $viewOrphanedFiles;
    }
}
