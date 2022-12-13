<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace report_sphorphanedfiles\Handler;

use cm_info;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\HTML;
use report_sphorphanedfiles\Misc;

defined('MOODLE_INTERNAL') || die();

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

    public function bind($user, $course, $instance, $page): Handler {
        $this->user = $user;
        $this->course = $course;
        $this->instance = $instance;
        $this->page = $page;

        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function getCourse() {
        return $this->course;
    }

    public function getInstance() {
        return $this->instance;
    }

    public function getPage() {
        return $this->page;
    }

    public function getIconHTML() {
        return HTML::createIconForInstance($this->getInstance(), $this->getPage());
    }

    public function addOrphans($orphans) {
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
    public function getIntro(cm_info $instance): string {
        $dbParams = ['id' => $instance->instance];

        if ($page = $this->getManager()->database()->getDbM()->get_record($this->getComponentName(), $dbParams, '*')) {
            return format_module_intro($this->getComponentName(), $page, $instance->id, false);
        }

        return "";
    }

    /**
     * @override
     */
    protected function enumerateFiles($user, $context, $course, $module): array {
        $result = $this->getManager()->database()->dataFiles()->getFilesForComponent($context, $module) ?? [];
        return $this->postFilter($result);
    }

    protected function generateViewFile($orphanedFile) {
        return $this->getManager()->files()->generateViewFile($orphanedFile);
    }

    /**
     * @override
     */
    public function getPreviewForFile(FileInfo $fileInfo) {
        $orphanedFile = $this->getManager()->files()->getFileUsingPathnamehash($fileInfo->getPathnamehash());

        if ($orphanedFile && $orphanedFile->is_valid_image()) {
            return $this->generateViewFile($orphanedFile);
        } else {
            return parent::getPreviewForFile($fileInfo);
        }
    }

    /**
     * @param FileInfo $formDelete
     * @param $file
     * @param $instance
     * @param $data
     * @return array containing all information about the file
     */
    protected function getSkeleton(FileInfo $formDelete, $file, $instance, $data): array {
        $result = $data;
        $result['modurl'] = $this->getModuleURLForInstance($instance);
        $result['filename'] = $this->getFileName(new FileInfo($formDelete));
        $result['preview'] = $this->getPreviewForFile(new FileInfo($formDelete));
        $result['filesize'] = Misc::convertByteInMegabyte((int)$file->filesize);
        return $result;
    }
}
