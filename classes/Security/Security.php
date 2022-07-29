<?php

namespace report_sphorphanedfiles\Security;

use coding_exception;
use context_course;
use moodle_database;
use moodle_exception;
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
     * @param FileInfo $fileInfo
     * @param int $courseId
     * @return bool
     * @throws coding_exception
     */
    public function isCourseIdOfFileSameLikeCourseidOfTheCourse(FileInfo $fileInfo, int $courseId): bool
    {
        // get the contextid of the file
        $fileContextId = $fileInfo->getContextId();
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
    public function allowedToViewDeleteAllFiles($courseId, $user): bool
    {
        $coursecontext = context_course::instance($courseId);
        // here you can change the roles or capabilities of who can view and delete the orphaned files
        return is_enrolled($coursecontext, $user, 'moodle/course:manageactivities') || is_siteadmin();
    }

    /**
     * @throws coding_exception
     * @throws moodle_exception
     * @throws require_login_exception
     */
    public function userIsAllowedToViewTheCourse($courseId)
    {
        $params = ['id' => $courseId];
        $course = $this->dbM->get_record('course', $params, '*', MUST_EXIST);
        // validate if the user is allowed to view this course
        require_login($course);
    }
}
