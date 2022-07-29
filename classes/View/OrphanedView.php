<?php

namespace report_sphorphanedfiles\View;

use stdClass;
use moodle_database;
use context_course;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\Manager;
use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\HTML;

/**
 * Class OrphanedView
 */
class OrphanedView
{
    private $page;

    /**
     * @var int
     */
    private $courseId;

    /**
     * @var stdClass
     */
    private $user;

    /**
     * @var Manager
     */
    private $apiM;

    /**
     * @var bool
     */
    private $afterDeletion = false;

    /**
     * indicates if a course uses gridformat-plugin
     * 
     * @var bool
     */
    private $courseFormatGridEnabled = false;

    /**
     * OrphanedView constructor.
     * @param moodle_database $db
     * @param int $courseId
     * @param moodle_page $page
     * @param bootstrap_renderer $output
     * @param stdClass $user
     */
    public function __construct($db, int $courseId, $page, $output, $user)
    {
        $this->courseId = $courseId;
        $this->user = $user;
        $this->apiM = new Manager($db);

        $course = $this->apiM->database()->dataFiles()->getCourse($courseId);

        $this->page = new Page($page, $course, $courseId, $output);
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * if the page is opened with a POST request,
     * this means the user has confirmed to delete a single orphaned file,
     * then we are checking if the file belongs to the user and delete it
     *
     * @return void
     * @throws coding_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public function deleteOrphanedFile(): void
    {
        // validate if the user is logged in and allowed to view the course
        // this method throws an exception if the user is not allowed
        $this->apiM->security()->userIsAllowedToViewTheCourse($this->courseId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (FileInfo::isSufficientForConstruction($_POST)) {
                $fileInfo = new FileInfo($_POST);
            }
            // Check for contextmanipulation of the course
            $isCourseIdOfFileSameLikeCourseidOfTheCourse = $this->apiM->security()->isCourseIdOfFileSameLikeCourseidOfTheCourse($fileInfo, $this->courseId);
            if (!$isCourseIdOfFileSameLikeCourseidOfTheCourse) {
                return;
            }
            $this->afterDeletion = $this->apiM->files()->deleteFileByUserInCourse(
                $this->apiM->security(),
                $fileInfo,
                $this->user,
                $this->courseId
            );
        }
    }

    public function listOrphansForSection($sectionInfo)
    {
        $courseContextId = context_course::instance($this->courseId)->id;

        $viewOrphanedFiles = [];
        $viewOrphanedFiles = $this->apiM->handler()->sectionSummaryHandler()->getViewOrphanedFiles(
            $viewOrphanedFiles,
            $courseContextId,
            $sectionInfo,
            $this->user,
            $this->courseId,
            '' // Intentionally left blank: In case of a section summary, there is no iconHtml information
        );

        $modInfo = $sectionInfo->modinfo;

        foreach ($modInfo->instances as $instances) {
            foreach ($instances as $instance) {
                if ($sectionInfo->id === $instance->section) {
                    if ($instance->deletioninprogress !== '1') {
                        if ($this->apiM->handler()->hasHandlerFor($instance)) {
                            $viewOrphanedFiles = $this->apiM->handler()->getHandlerFor($instance)
                                ->bind($this->user, $this->courseId, $instance, $this->getPage())
                                ->addOrphans($viewOrphanedFiles);
                        }
                    }
                }
            }
        }

        return $viewOrphanedFiles;
    }

    public function createOrphansList($sectionInfo, $usingTemplate): string
    {
        $viewOrphanedFiles = $this->listOrphansForSection($sectionInfo);

        $cleanedViewOrphanedFiles = []; 
        foreach ($viewOrphanedFiles ?? [] as $viewOrphanedFile){
            if (!($this->courseFormatGridEnabled && isset($viewOrphanedFile['isGridlayoutFile']) && $viewOrphanedFile['isGridlayoutFile'])) {
                $cleanedViewOrphanedFiles[] = $viewOrphanedFile;
            }
        }

        if (!empty($cleanedViewOrphanedFiles)) {
            $translations = Misc::translate(['isallowedtodeleteallfiles', 'description', 'isgridlayoutfilehint'], 'report_sphorphanedfiles');
            $translations['header'] = Misc::translate(['modName', 'content', 'filename', 'preview', 'tool', 'moduleContent', 'code'], 'report_sphorphanedfiles', 'header.');

            return $this->getPage()->getOutput()->render_from_template(
                $usingTemplate,
                ['orphanedFiles' => $cleanedViewOrphanedFiles, 'translation' => $translations],
            );
        }

        return "";
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public function init()
    {
        if ( isset($this->getPage()->getCourse()->format) && $this->getPage()->getCourse()->format === 'grid' ) {
            $this->courseFormatGridEnabled = true;
        }

        // validate if the user is logged in and allowed to view the course
        // this method throws an exception if the user is not allowed
        $this->apiM->security()->userIsAllowedToViewTheCourse($this->courseId);

        $allowedToViewDeleteAllFiles = $this->apiM->security()->allowedToViewDeleteAllFiles(
            $this->courseId,
            $this->user
        );

        echo $this->getPage()->getOutput()->header();
        echo $this->getPage()->getOutput()->render_from_template(
            'report_sphorphanedfiles/report',
            [
                'title' => $this->getPage()->getTitle(),
                'allowedToViewDeleteAllFiles' => $allowedToViewDeleteAllFiles,
                'afterDeletion' => $this->afterDeletion,
                'deleteMessage' => get_string('deleteMessage', 'report_sphorphanedfiles'),
                'translation' => Misc::translate(['isallowedtodeleteallfiles', 'description', 'isgridlayoutfilehint'], 'report_sphorphanedfiles')
            ]
        );

        $sectionCounter = 0;

        foreach ($this->getPage()->getCourseInfo()->get_section_info_all() as $sectionInfo) {
            $mustache_name = 'report_sphorphanedfiles/sectionTable';
            echo HTML::createSectionOverview(
                3,
                HTML::createSectionHeading($sectionInfo, $this->getPage()->getCourse(), $sectionCounter++),
                $this->createOrphansList($sectionInfo, $mustache_name)
            );
        }

        echo $this->getPage()->getOutput()->footer();
    }
}
