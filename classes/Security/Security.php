<?php

namespace report_sphorphanedfiles\Security;

use coding_exception;
use context_course;
use moodle_database;
use moodle_exception;
use require_login_exception;
use stdClass;

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
