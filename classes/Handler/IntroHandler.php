<?php

namespace report_sphorphanedfiles\Handler;

use stdClass;
use cm_info;
use dml_exception;

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
     * @override
     */
    protected function enumerateFiles($user, $context, $course, $module): array
    {
        if ($this->isUserAllowedToViewDeleteAllFilesForCourse($user, $course)) {
            $result = $this->getManager()->database()->dataFiles()->getFilesForComponentIntro($context, $module) ?? [];
        } else {
            $result = $this->getManager()->database()->dataFiles()->getFilesOfUserForComponentIntro($user->id, $context, $module) ?? [];
        }

        return $this->postFilter($result);
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
        $instance,
        $iconHtml
    ): array {

        // FIXME: Das ist nicht die optimal passende Stelle für die Instanzvariablen-
        //        zuweisung. Verdeckte Abhängigkeit: getIntro nutzt getComponentName-
        //        Interface
        $this->componentName = $instance->modname;

        $htmlContent = $this->getIntro($instance);

        $name = $instance->name;


        $userAllowedToDelete = $this->isUserAllowedToViewDeleteAllFilesForCourse($user, $courseId);
        $orphanedFiles = $this->enumerateOrphanedFilesFromString($user, $contextId, $courseId, $htmlContent, $this->getComponentName());

        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);

            $viewOrphanedFiles[] = $this->getSkeleton($formDelete,$file,$instance,[
                'modName' => $this->getComponentName(),
                'name' => $name,
                'instanceId' => $instance->id,
                'contextId' => $contextId,
                'content' => $htmlContent,
                'userAllowedToDelete' => $userAllowedToDelete,
                'iconHtml' => $iconHtml,
            ]);
        }

        return $viewOrphanedFiles;
    }
}
