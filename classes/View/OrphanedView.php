<?php

namespace report_sphorphanedfiles\View;

use stdClass;
use moodle_database;
use moodle_url;
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
    /**
     * @param moodle_database
     */
    private $db;

    /**
     * @var int
     */
    private $courseId;

    /**
     * @var moodle_page
     */
    private $page;

    /**
     * @var bootstrap_renderer
     */
    private $output;

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
     * OrphanedView constructor.
     * @param moodle_database $db
     * @param int $courseId
     * @param moodle_page $page
     * @param bootstrap_renderer $output
     * @param stdClass $user
     */
    public function __construct($db, int $courseId, $page, $output, $user)
    {
        $this->db = $db;
        $this->courseId = $courseId;
        $this->page = $page;
        $this->output = $output;
        $this->user = $user;
        $this->apiM = new Manager($this->db);
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

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            FileInfo::isSufficientForConstruction($_POST)
        ) {
            $this->afterDeletion = $this->apiM->files()->deleteFileByUserInCourse(
                $this->apiM->security(),
                new FileInfo($_POST),
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
            "" // Intentionally left blank: In case of a section summary, there is no iconHtml information
        );

        $modInfo = $sectionInfo->modinfo;

        foreach ($modInfo->instances as $instances) {
            foreach ($instances as $instance) {
                if ($sectionInfo->id === $instance->section) {
                    if ($instance->deletioninprogress !== '1') {
                        if ($this->apiM->handler()->hasHandlerFor($instance)) {
                            $viewOrphanedFiles = $this->apiM->handler()->getHandlerFor($instance)
                                ->bind($this->user, $this->courseId, $instance, $this->page)
                                ->addOrphans($viewOrphanedFiles);
                        }
                    }
                }
            }
        }

        return $viewOrphanedFiles;
    }

    public function renderOrphans($sectionInfo, $usingTemplate)
    {
        $viewOrphanedFiles = $this->listOrphansForSection($sectionInfo);

        if (!empty($viewOrphanedFiles)) {
            $translations = Misc::translate(['isallowedtodeleteallfiles', 'description', 'moduleContent'], 'report_sphorphanedfiles');
            $translations['header'] = Misc::translate(['modName', 'content', 'filename', 'preview', 'tool'], 'report_sphorphanedfiles', 'header.');

            echo $this->output->render_from_template(
                $usingTemplate,
                ['orphanedFiles' => $viewOrphanedFiles, 'translation' => $translations]
            );
        }
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public function init()
    {
        // validate if the user is logged in and allowed to view the course
        // this method throws an exception if the user is not allowed
        $this->apiM->security()->userIsAllowedToViewTheCourse($this->courseId);

        $allowedToViewDeleteAllFiles = $this->apiM->security()->allowedToViewDeleteAllFiles(
            $this->courseId,
            $this->user
        );

        $params = ['id' => $this->courseId];
        $course = $this->apiM->database()->getDbM()->get_record('course', $params, '*', MUST_EXIST);

        $title = get_string('pluginname', 'report_sphorphanedfiles');
        $this->setPageMetaData($course, $title);

        echo $this->output->header();

        echo $this->output->render_from_template(
            'report_sphorphanedfiles/report',
            [
                'title' => $title,
                'allowedToViewDeleteAllFiles' => $allowedToViewDeleteAllFiles,
                'afterDeletion' => $this->afterDeletion,
                'deleteMessage' => get_string('deleteMessage', 'report_sphorphanedfiles'),
                'translation' => Misc::translate(['isallowedtodeleteallfiles', 'description'], 'report_sphorphanedfiles')
            ]
        );

        $courseInfo = get_fast_modinfo($course);
        $sectionCounter = 0;

        foreach ($courseInfo->get_section_info_all() as $sectionInfo) {
            echo '<div class="border shadow p-1">';

            echo HTML::createSectionHeading($sectionInfo, $course, $sectionCounter++);

            //
            // Classic View: 'report_sphorphanedfiles/sectionTable'
            // Multi Selection: 'report_sphorphanedfiles/sectionTableMultipleSelection'
            //
            $this->renderOrphans($sectionInfo,'report_sphorphanedfiles/sectionTableMultipleSelection');

            echo "</div><br /><br /><br />";
        }

        echo $this->output->footer();
    }

    /**
     * @param stdClass $course
     * @param string $title
     * @throws coding_exception
     * @throws moodle_exception
     */
    private function setPageMetaData($course, $title)
    {
        $urlparams = array('id' => $this->courseId);
        $url = new moodle_url('/report/sphorphanedfiles/index.php', $urlparams);
        $this->page->set_url($url);
        $this->page->set_title($title);
        $this->page->set_heading($course->fullname);
        $this->page->set_pagelayout('incourse');
    }
}
