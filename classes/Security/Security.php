<?php

namespace report_sphorphanedfiles\Security;

use coding_exception;
use context_course;
use moodle_database;
use moodle_exception;
use report_sphorphanedfiles\Files\Files;
use report_sphorphanedfiles\Files\FileInfo;
use require_login_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class Security
 */
class Security
{
    /**
     * @var moodle_database
     */
    private $dbM;

    /**
     * Security constructor.
     * @param moodle_database $dbM
     */
    public function __construct(moodle_database $dbM)
    {
        $this->dbM = $dbM;
    }



        /**
     * @param Files $fileToBeDeleted
     * @param int $courseId
     * @return bool
     * @throws coding_exception
     */
    public function isCourseIdOfFileSameLikeCourseidOfTheCourse(\stored_file $fileToBeDeleted, int $courseId): bool
    {
        // get the contextid of the file
        $fileContextId = $fileToBeDeleted->get_contextid();
        // now get the context of the modul where te file belongs to
        $contextOfFile = \context::instance_by_id($fileContextId, MUST_EXIST);
        // Now get the context of the course (files that belongs to sectionsummarys for example are allreade coursecontext
        $courseContext = $contextOfFile->get_course_context();

        // now get the context of the course the module and there for the file belongs to
        $courseContextId = $courseContext->id;
        // now get the courseid of the file that we get by post and is stored in fileinfo
        $courseIdOfFile = $courseContext->instanceid;

        // Compare the courseID of the file with the course id of the user
        // echo '$course ' . $course . "<br>";
        // echo '$courseIdOfFile ' . $courseIdOfFile . "<br>";
        // Only if course has the same id as the courseid of the file
        if ($courseId != $courseIdOfFile) {
            return false;
        }
        return true;
    }

    /**
     * A user that is enrolled in the course and has the the capabilty moodle/course:manageactivities
     * or is_siteadmin (ToDo: Is this not nessesary because siteadmins DOES have this capability???)
     * are allowed to delete all file in a course
     *
     * @param int $courseId
     * @param stdClass $user
     * @return bool
     */
    public function allowedToViewReport($courseId, $user): bool
    {
        $coursecontext = context_course::instance($courseId);
        // here you can change the roles or capabilities of who can view and delete the orphaned files
        return has_capability('moodle/course:manageactivities', $coursecontext)
            && has_capability('report/sphorphanedfiles:view', $coursecontext)
            && has_capability('report/sphorphanedfiles:delete', $coursecontext);
    }

    /**
     * User needs three capabilitys to be allowed to delete
     * moodle/course:manageactivities
     * report/sphorphanedfiles:view'
     * report/sphorphanedfiles:delete
     *
     * @param $courseId
     * @param $user
     * @return bool
     * @throws coding_exception
     */
    public function isUserAllowedToDeleteFiles($courseId, $user): bool
    {
        $coursecontext = context_course::instance($courseId);
        // here you can change the roles or capabilities of who can view and delete the orphaned files
        return has_capability('moodle/course:manageactivities', $coursecontext)
            && has_capability('report/sphorphanedfiles:view', $coursecontext)
            && has_capability('report/sphorphanedfiles:delete', $coursecontext);
    }

}
