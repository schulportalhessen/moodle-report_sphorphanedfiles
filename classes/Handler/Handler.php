<?php

namespace report_sphorphanedfiles\Handler;

use cm_info;
use ReflectionClass;

use moodle_url;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\Manager;

/**
 * This class should always be used as super class for all handlers, i.e. concrete
 * handler implementations for different Moodle objects -- which should be scanned
 * for orphaned parts -- should extend this class.
 * 
 * All functionality common to any kind of handler should reside inside this class
 * to avoid code redundancy.
 */
abstract class Handler
{
    /**
     * @var Manager
     */
    protected $apiM;

    /**
     * Initialize, i.e. bind, the class to the corresponding Manager instance.
     * 
     * @param Manager $apiM The Manager instance to be used by this instance.
     */
    public function __construct(Manager $apiM)
    {
        $this->apiM = $apiM;
    }

    /** 
     *  Return the Manager instance this handler is bound to.
     * 
     *  @return Manager The bound Manager instance.
     */
    public function getManager(): Manager
    {
        return $this->apiM;
    }

    /** Returns the component's name as required in the context of the Moodle system.
     *  Using reflection, the correct name can be determined automagically if
     *  subclasses use the „standard“ naming convention.
     * 
     *  Naming convention: Use class names postfixed with „Handler“, e.g. 
     *                     PageHandler --- automagically --> page
     * 
     *  Attention: If performance is important, you might override this generic default
     *             implementation.
     * 
     * The component's name matching Moodle requirements.
     *  @return string 
     * 
     */
    public function getComponentName(): string
    {
        $mySimpleName = (new ReflectionClass($this))->getShortName();

        return strtolower(substr($mySimpleName, 0, strpos($mySimpleName, "Handler")));
    }

    /**
     * Retrieves, i.e. extracts, the intro information of the given instance.
     * 
     * @param cm_info $instance The instance whose intro information should be extracted.
     * 
     * @return string The intro of the instance as HTML content OR an empty string if this
     *                information does not exist.
     */
    public function getIntro(cm_info $instance): string
    {
        $dbParams = ['id' => $instance->instance];

        if ($page = $this->apiM->database()->getDbM()->get_record($this->getComponentName(), $dbParams, '*')) {
            return format_module_intro($this->getComponentName(), $page, $instance->id, false);
        }

        return "";
    }

    /**
     * Checks if the given users is allowed to delete (all) files in this course.
     * 
     * @param $user   The user for which the check should be performed.
     * @param $course The course for which to check.
     * 
     * @return true if user has appropriate rights, false otherwise.
     */
    public function isUserAllowedToViewDeleteAllFilesForCourse($user, $course): bool
    {
        return $this->getManager()->security()->allowedToViewDeleteAllFiles($course, $user);
    }

    /**
     * Enumerates all files the given user is allowed to perform Moodle actions on, the
     * special file „.“ is filtered and therefore not an element of the returned array.
     * 
     * @param $user The user for which the enumeration has to be generated.
     * 
     * 
     * @return array An array containing the relevant files OR an empty array if no such
     *               files exist.
     * 
     */
    public function enumerateFilesForUserInContextForModuleInCourse($user, $context, $module, $course): array
    {
        if ($this->isUserAllowedToViewDeleteAllFilesForCourse($user, $course)) {
            $result = $this->getManager()->database()->dataFiles()->getFilesForComponent($context, $module) ?? [];
        } else {
            $result = $this->getManager()->database()->dataFiles()->getFilesOfUserForComponent($user->id, $context, $module) ?? [];
        }

        return array_filter(
            $result,
            function ($file, $key) {
                return $file->filename !== '.';
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Enumerates all files that are orphaned with respect to the given HTML content.
     * 
     * @param $user The user for which the enumeration has to be generated.
     * 
     * 
     * @return array An array containing the relevant files OR an empty array if no such
     *               files exist.
     * 
     */
    public function enumerateOrphanedFilesFromString($user, $context, $module, $course, $htmlContent): array
    {
        return $this->getManager()->parser()->extractOrphanedFilesFromString(
            $htmlContent,
            $this->enumerateFilesForUserInContextForModuleInCourse($user, $context, $module, $course),
            $context
        );
    }

    public function getPreviewForFile(FileInfo $fileInfo, $globalConfig)
    {
        $orphanedFile = $this->getManager()->files()->getFileUsingFileInfo($fileInfo);

        if ($orphanedFile && $orphanedFile->is_valid_image()) {
            return $this->getManager()->files()->generateViewFile(
                $orphanedFile,
                $globalConfig
            );
        } else {
            return $this->apiM->files()->generateFallbackView(
                $orphanedFile,
                $globalConfig
            );
        }
    }

    public function getFileName(FileInfo $fileInfo, $globalConfig)
    {
        return $this->getManager()->files()->generateFallbackView(
            $this->getManager()->files()->getFileUsingFileInfo($fileInfo),
            $globalConfig
        );
    }

    public function getModuleURLForInstance($instance)
    {
        return new moodle_url('/mod/' . $instance->modname . '/view.php?id=' . $instance->id);
    }

    abstract public function getViewOrphanedFiles($viewOrphanedFiles, $contextId, $user, $courseId, $globalCfg, $instance, $iconHtml): array;
}
