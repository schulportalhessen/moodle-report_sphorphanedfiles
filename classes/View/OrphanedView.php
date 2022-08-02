<?php

namespace report_sphorphanedfiles\View;

use Dompdf\Exception;
use stdClass;
use moodle_database;
use context_course;

use report_sphorphanedfiles\Files\Files;
use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\Manager;
use report_sphorphanedfiles\Misc;
use report_sphorphanedfiles\HTML;

use UnexpectedValueException;

defined('MOODLE_INTERNAL') || die();

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
        // Deleting a file is requested bei Post-request. The courseid id is already a required paramter. Now read an identifyer
        // for the file that should be deleted and check for the capabilitiy and do seurity things
        // red the variables from the post-request
        // check the values if they are correct and secure
        $pathnamehash = required_param('pathnamehash', PARAM_ALPHANUM);
        if (strlen($pathnamehash) > 40 ) {
            // pathnamehash must be of type VARCHAR (40)
            throw new UnexpectedValueException('wrong pathnamehash');
            return;
        }
        // Get the file that might should be deleted
        $fileToBeDeleted = (new Files())->getFileStorage()->get_file_by_hash($pathnamehash);
        if (!$fileToBeDeleted) {
            // If file was already deleted
            throw new UnexpectedValueException('file not found');
            return;
        }

        // Check if file has the context that belongs to the course the user has courseaccess
        if (!$this->apiM->security()->isCourseIdOfFileSameLikeCourseidOfTheCourse($fileToBeDeleted, $this->courseId)) {
            throw new UnexpectedValueException('wrong value found');
            return;
        }

        // compare fileId from Post with $fileToBeDeleted-Information
        // ToDo: some more securitychecks on the Post-Parameter
        $fileID = required_param('fileID', PARAM_TEXT);

        // get the contextid of the file
        $dataFileToBeDeleted = [];
        $dataFileToBeDeleted['pathnamehash'] = $fileToBeDeleted->get_pathnamehash();
        $dataFileToBeDeleted['contextId'] = $fileToBeDeleted->get_contextid();
        $dataFileToBeDeleted['component'] = $fileToBeDeleted->get_component();
        $dataFileToBeDeleted['filearea'] = $fileToBeDeleted->get_filearea();
        $dataFileToBeDeleted['itemId'] = $fileToBeDeleted->get_itemid();
        $dataFileToBeDeleted['filepath'] = $fileToBeDeleted->get_filepath();
        $dataFileToBeDeleted['filename'] = $fileToBeDeleted->get_filename();

        $serialisationFileToBeDeleted = (new FileInfo($dataFileToBeDeleted))->toString();


        if ($serialisationFileToBeDeleted != $fileID) {
            // files are not equal ...
            throw new UnexpectedValueException('wrong value found');
            return;
        }

        $this->afterDeletion = $this->apiM->files()->deleteFileByUserInCourse(
            $this->apiM->security(),
            $fileToBeDeleted,
            $this->user,
            $this->courseId
        );
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

    public function createOrphansList($sectionInfo): string
    {
        $viewOrphanedFiles = $this->listOrphansForSection($sectionInfo);
        $cleanedViewOrphanedFiles = [];
        // Do not mark plugin gridlayout files as orphaned
        foreach ($viewOrphanedFiles ?? [] as $viewOrphanedFile){
            if (!($this->courseFormatGridEnabled && isset($viewOrphanedFile['isGridlayoutFile']) && $viewOrphanedFile['isGridlayoutFile'])) {
                $cleanedViewOrphanedFiles[] = $viewOrphanedFile;
            }
        }

        if (!empty($cleanedViewOrphanedFiles)) {
            $translations = Misc::translate(['isallowedtodeleteallfiles', 'description', 'isgridlayoutfilehint'], 'report_sphorphanedfiles');
            $translations['header'] = Misc::translate(['modName', 'content', 'filename', 'preview', 'tool', 'moduleContent', 'code'], 'report_sphorphanedfiles', 'header.');

            $dummy = $this->getPage()->getOutput()->render_from_template(
                'report_sphorphanedfiles/sectionTable',
                [
                    'orphanedFilesList' => $cleanedViewOrphanedFiles,
                    'translation' => $translations
                ],
            );
            return $dummy;
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

        $allowedToViewDeleteAllFiles = $this->apiM->security()->allowedToViewDeleteAllFiles(
            $this->courseId,
            $this->user
        );

        $isUserAllowedToDeleteFiles = $this->apiM->security()->isUserAllowedToDeleteFiles(
            $this->courseId,
            $this->user
        );
        echo $this->getPage()->getOutput()->header();
        // Render content above the table
        echo $this->getPage()->getOutput()->render_from_template(
            'report_sphorphanedfiles/report',
            [
                'title' => $this->getPage()->getTitle(),
                'allowedToViewDeleteAllFiles' => $allowedToViewDeleteAllFiles,
                'isUserAllowedToDeleteFiles' => $isUserAllowedToDeleteFiles,
                'afterDeletion' => $this->afterDeletion,
                'deleteMessage' => get_string('deleteMessage', 'report_sphorphanedfiles'),
                'translation' => Misc::translate(['isallowedtodeleteallfiles', 'description', 'isgridlayoutfilehint'], 'report_sphorphanedfiles')
            ]
        );

        // Now render each section
        $sectionCounter = 0;
        foreach ($this->getPage()->getCourseInfo()->get_section_info_all() as $sectionInfo) {
            echo HTML::createSectionOverview(
                3,
                HTML::createSectionHeading($sectionInfo, $this->getPage()->getCourse(), $sectionCounter++),
                $this->createOrphansList($sectionInfo)
            );
        }

        echo $this->getPage()->getOutput()->footer();
    }
}
