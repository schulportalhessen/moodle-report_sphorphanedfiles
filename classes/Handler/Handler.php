<?php

namespace report_sphorphanedfiles\Handler;

use cm_info;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\HTML;
use report_sphorphanedfiles\Misc;
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
    private $user;
    private $course;
    private $instance;
    private $page;

    public function bind($user, $course, $instance, $page): Handler
    {
        $this->user = $user;
        $this->course = $course;
        $this->instance = $instance;
        $this->page = $page;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getIconHTML()
    {
        return HTML::createIconForInstance($this->getInstance(), $this->getPage());
    }

    public function addOrphans($orphans)
    {
        return $this->getViewOrphanedFiles(
            $orphans,
            $this->getInstance()->context->id,
            $this->getUser(),
            $this->getCourse(),
            $this->getInstance(),
            $this->getIconHTML()
        );
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

    protected function getSkeleton(FileInfo $formDelete, $file, $instance, $data): array
    {
        $result = $formDelete->addFileReferenceInformation($data);

        $result['modurl'] = $this->getModuleURLForInstance($instance);
        $result['filename'] = $this->getFileName(new FileInfo($formDelete));
        $result['preview'] = $this->getPreviewForFile(new FileInfo($formDelete));
        $result['filesize'] = Misc::convertByteInMegabyte((int)$file->filesize);

        return $result;
    }
}
