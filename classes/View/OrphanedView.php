<?php

namespace report_sphorphanedfiles\View;

use stdClass;
use moodle_database;
use moodle_url;
use context_course;
use html_writer;

use report_sphorphanedfiles\Files\FileInfo;
use report_sphorphanedfiles\Manager;

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
     * @param stdClass $globalCfg
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

        $deleteMessage = get_string('deleteMessage', 'report_sphorphanedfiles');


        $translations = [
            'isallowedtodeleteallfiles' => get_string('isallowedtodeleteallfiles', 'report_sphorphanedfiles'),
            'description' => get_string('description', 'report_sphorphanedfiles')
        ];

        echo $this->output->render_from_template(
            'report_sphorphanedfiles/report',
            [
                'title' => $title,
                'allowedToViewDeleteAllFiles' => $allowedToViewDeleteAllFiles,
                'afterDeletion' => $this->afterDeletion,
                'deleteMessage' => $deleteMessage,
                'translation' => $translations
            ]
        );

        $courseInfo = get_fast_modinfo($course);
        $sectionCounter = 0;

        $formatsectionname = '';
        if (get_string_manager()->string_exists('sectionname', 'format_' . $course->format)) {
            $formatsectionname = get_string('sectionname', 'format_' . $course->format);
        }


        foreach ($courseInfo->get_section_info_all() as $sectionInfo) {
            echo '<div class="border shadow p-1">';
            $url = (new moodle_url('/course/view.php', array('id' => $courseInfo->courseid))) . '#section-' . $sectionCounter;
            $sectionname = $sectionInfo->name;
            $anzuzeigenderText = '';
            if (is_null($sectionname) || $sectionname === '') {
                $anzuzeigenderText = $formatsectionname . ' ' . $sectionCounter;
            } else {
                $anzuzeigenderText = $sectionname;
            }

            $linktext = html_writer::link($url, $anzuzeigenderText);
            $linktext2 = html_writer::link($url, '📑', ['target' => '_blank']);
            echo html_writer::tag('h3', '(' . $sectionCounter . ') ' . $linktext . ' ' .  $linktext2, ['class' => 'orphandfilesh3']);

            $sectionCounter++;
            $modInfo = $sectionInfo->modinfo;
            $viewOrphanedFiles = [];

            // section info orphaned files
            $courseContext = context_course::instance($this->courseId);
            $courseContextId = $courseContext->id;

            // CHECKME: Ist das außerhalb der Schleife notwendig? Falls ja: Das sollte in den Dispatcher wandern.
            $viewOrphanedFiles = $this->apiM->handler()->sectionSummaryHandler()->getViewOrphanedFiles(
                $viewOrphanedFiles,
                $courseContextId,
                $sectionInfo,
                $this->user,
                $this->courseId,
                "" // Bewusste Setzung: Keine iconHtml-Informationen festsetzen, da hier nicht vorhanden.
            );

            foreach ($modInfo->instances as $instances) {
                foreach ($instances as $instance) {
                    if ($sectionInfo->id === $instance->section) {
                        $context = $instance->context;
                        $url = $this->page->theme->image_url('icon', $instance->modname)->out();
                        $style = 'width: 20px; height: 20px; margin-right: 4px;';
                        $cssclass = 'iconlarge activityicon';
                        $iconHtml = \html_writer::tag('img', '', array('src' => $url, 'style' => $style, 'class' => $cssclass));

                        if ($instance->deletioninprogress !== '1') {
                            if ($this->apiM->hasHandlerFor($instance->modname)) {
                                $viewOrphanedFiles = $this->apiM->getHandlerFor($instance->modname)->getViewOrphanedFiles(
                                    $viewOrphanedFiles,
                                    $context->id,
                                    $this->user,
                                    $this->courseId,
                                    $instance,
                                    $iconHtml
                                );
                            }
                        }
                    }
                }
            }

            if (!empty($viewOrphanedFiles)) {
                $translations = [
                    'header' => [
                        'modName' => get_string('header.modName', 'report_sphorphanedfiles'),
                        'content' => get_string('header.content', 'report_sphorphanedfiles'),
                        'filename' => get_string('header.filename', 'report_sphorphanedfiles'),
                        'preview' => get_string('header.preview', 'report_sphorphanedfiles'),
                        'tool' => get_string('header.tool', 'report_sphorphanedfiles'),
                    ],
                    'isallowedtodeleteallfiles' => get_string('isallowedtodeleteallfiles', 'report_sphorphanedfiles'),
                    'description' => get_string('description', 'report_sphorphanedfiles'),
                    'moduleContent' => get_string('moduleContent', 'report_sphorphanedfiles')
                ];
                echo $this->output->render_from_template(
                    'report_sphorphanedfiles/sectionTable',
                    ['orphanedFiles' => $viewOrphanedFiles, 'translation' => $translations]
                );
            }
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
