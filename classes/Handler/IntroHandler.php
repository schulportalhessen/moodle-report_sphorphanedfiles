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
        global $CFG;

        if (isset($CFG->report_sphorphanedfiles_handleractivitiescore) && in_array($component, explode(',', $CFG->report_sphorphanedfiles_handleractivitiescore))) {
            return true;
        }
        if (isset($CFG->report_sphorphanedfiles_handleractivitiesplugin) && in_array($component, explode(',', $CFG->report_sphorphanedfiles_handleractivitiesplugin))) {
            return true;
        }
        if (isset($CFG->report_sphorphanedfiles_handlermaterialscore) && in_array($component, explode(',', $CFG->report_sphorphanedfiles_handlermaterialscore))) {
            return true;
        }
        if (isset($CFG->report_sphorphanedfiles_handlermaterialsplugin) && in_array($component, explode(',', $CFG->report_sphorphanedfiles_handlermaterialsplugin))) {
            return true;
        }
    
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

        $componentName = $this->getComponentName();
        // echo $componentName . ': '.  count($orphanedFiles) . '<br />';
        foreach ($orphanedFiles as $file) {
            $formDelete = (new FileInfo())->setFromFileWithContext($file, $contextId);

            $viewOrphanedFiles[] = $this->getSkeleton($formDelete, $file, $instance, [
                'modName' => $componentName,
                'name' => $name." id=".$instance->id,
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
