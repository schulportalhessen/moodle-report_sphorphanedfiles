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
    private const URLPattern = "/mod/%s/view.php?id=%s";

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
     *  Naming convention: Use class names postfixed with the name of this base class, .i.e. 
     *                     Handler. For example:
     * 
     *                     In case of subclass PageHandler: PageHandler --- automagically --> page
     * 
     *  Attention: **Keep in mind that Reflection is not necessarily slow in PHP!**
     * 
     *               ---> https://stackoverflow.com/a/54502334
     * 
     *             If you really think performance is an issue in the context of this method,
     *             you **might** override this generic default implementation.
     * 
     * The component's name matching Moodle requirements.
     *  @return string 
     * 
     */
    public function getComponentName(): string
    {
        // Safety in case of renaming. Always use the exact name of the base class, not any
        // hard-coded string.
        $theBaseClassName = (new ReflectionClass(self::class))->getShortName();
        $mySimpleName = (new ReflectionClass($this))->getShortName();

        return strtolower(substr($mySimpleName, 0, strpos($mySimpleName, $theBaseClassName)));
    }

    public function canHandle(string $type): bool
    {
        return $this->getComponentName() === $type;
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

        if ($page = $this->getManager()->database()->getDbM()->get_record($this->getComponentName(), $dbParams, '*')) {
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

    public function postFilter(array $data): array
    {
        return array_filter(
            $data,
            function ($file, $key) {
                return $file->filename !== '.';
            },
            ARRAY_FILTER_USE_BOTH
        );
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

        return $this->postFilter($result);
    }

    public function enumerateFilesForUserInContextForModuleInCourseIntro($user, $context, $module, $course): array
    {
        if ($this->isUserAllowedToViewDeleteAllFilesForCourse($user, $course)) {
            $result = $this->getManager()->database()->dataFiles()->getFilesForComponentIntro($context, $module) ?? [];
        } else {
            $result = $this->getManager()->database()->dataFiles()->getFilesOfUserForComponentIntro($user->id, $context, $module) ?? [];
        }

        return $this->postFilter($result);
    }

    public function enumerateFilesForUserInSectionSummary($user, $course, $courseContextId, $fileItemIdSectionInfo): array
    {
        if ($this->isUserAllowedToViewDeleteAllFilesForCourse($user, $course)) {
            $result = $this->apiM->database()->dataFiles()->getFilesForSectionSummary($fileItemIdSectionInfo, $courseContextId) ?? [];
        } else {
            $result = $this->apiM->database()->dataFiles()->getFilesOfUserForSectionSummary($user->id, $courseContextId, $fileItemIdSectionInfo) ?? [];
        }

        return $this->postFilter($result);
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

    public function enumerateOrphanedFilesInIntroFromString($user, $context, $module, $course, $htmlContent): array
    {
        return $this->getManager()->parser()->extractOrphanedFilesFromString(
            $htmlContent,
            $this->enumerateFilesForUserInContextForModuleInCourseIntro($user, $context, $module, $course),
            $context
        );
    }

    public function enumerateOrphanedFilesInSectionSummary($user, $context, $fileItemIdSectionInfo, $course, $htmlContent): array
    {
        return $this->getManager()->parser()->extractOrphanedFilesFromString(
            $htmlContent,
            $this->enumerateFilesForUserInSectionSummary($user, $course, $context, $fileItemIdSectionInfo),
            $context
        );
    }

    protected function generateViewFile($orphanedFile)
    {
        return $this->getManager()->files()->generateViewFile($orphanedFile);
    }

    public function getPreviewForFile(FileInfo $fileInfo)
    {
        $orphanedFile = $this->getManager()->files()->getFileUsingFileInfo($fileInfo);

        if ($orphanedFile && $orphanedFile->is_valid_image()) {
            return $this->generateViewFile($orphanedFile);
        } else {
            return $this->getManager()->files()->generateFallbackView($orphanedFile);
        }
    }

    public function getFileName(FileInfo $fileInfo)
    {
        return $this->getManager()->files()->generateFallbackView(
            $this->getManager()->files()->getFileUsingFileInfo($fileInfo)
        );
    }

    public function getModuleURLForInstance($instance)
    {
        return new moodle_url(sprintf(self::URLPattern, $instance->modname, $instance->id));
    }

    abstract public function getViewOrphanedFiles($viewOrphanedFiles, $contextId, $user, $courseId, $instance, $iconHtml): array;
}
