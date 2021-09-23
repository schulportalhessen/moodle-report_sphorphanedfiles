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
abstract class Handler extends BaseHandler
{
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
     * @override
     */
    protected function enumerateFiles($user, $context, $course, $module): array
    {
        if ($this->isUserAllowedToViewDeleteAllFilesForCourse($user, $course)) {
            $result = $this->getManager()->database()->dataFiles()->getFilesForComponent($context, $module) ?? [];
        } else {
            $result = $this->getManager()->database()->dataFiles()->getFilesOfUserForComponent($user->id, $context, $module) ?? [];
        }

        return $this->postFilter($result);
    }

    protected function generateViewFile($orphanedFile)
    {
        return $this->getManager()->files()->generateViewFile($orphanedFile);
    }

    /**
     * @override
     */
    public function getPreviewForFile(FileInfo $fileInfo)
    {
        $orphanedFile = $this->getManager()->files()->getFileUsingFileInfo($fileInfo);

        if ($orphanedFile && $orphanedFile->is_valid_image()) {
            return $this->generateViewFile($orphanedFile);
        } else {
            return parent::getPreviewForFile($fileInfo);
        }
    }
}
