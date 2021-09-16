<?php

namespace report_sphorphanedfiles\Database;

use moodle_database;

/**
 * Class DataFiles
 * @package report_sphorphanedfiles\Database
 */
class DataFiles
{
    /**
     * @var moodle_database
     */
    private $dbM;

    /**
     * Files constructor.
     * @param moodle_database $dbM
     */
    public function __construct(moodle_database $dbM)
    {
        $this->dbM = $dbM;
    }

    /**
     * @param int $userId
     * @param int $contextId
     * @param string $modName
     * @return array
     * @throws \dml_exception
     */
    public function getFilesOfUserForComponent(int $userId, int $contextId, string $modName)
    {
        $wheres = ["userid = :userid", "contextid = :contextid", "component = :component"];

        $params['userid'] = $userId;
        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);

        $whereSql = implode(" AND ", $wheres);

        $sql = "SELECT * FROM {files} WHERE {$whereSql}";

        // Fetch the stats data.
        return $this->dbM->get_records_sql($sql, $params);
    }


 /**
     * @param int $userId
     * @param int $contextId
     * @param string $modName
     * @return array
     * @throws \dml_exception
     */
    public function getFilesOfUserForComponentIntro(int $userId, int $contextId, string $modName)
    {
        $wheres = ["userid = :userid", "contextid = :contextid", "component = :component",  "filearea = :filearea"];

        $params['userid'] = $userId;
        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);
        $params['filearea'] = 'intro';

        $whereSql = implode(" AND ", $wheres);

        $sql = "SELECT * FROM {files} WHERE {$whereSql}";

        // Fetch the stats data.
        return $this->dbM->get_records_sql($sql, $params);
    }


    /**
     * @param int $userId
     * @param int $contextId
     * @param string $modName
     * @return array
     * @throws \dml_exception
     */
    public function getFilesForComponent(int $contextId, string $modName)
    {
        $wheres = ["contextid = :contextid", "component = :component"];

        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);

        $whereSql = implode(" AND ", $wheres);

        $sql = "SELECT * FROM {files} WHERE {$whereSql}";

        // Fetch the stats data.
        return $this->dbM->get_records_sql($sql, $params);
    }

        /**
     * @param int $userId
     * @param int $contextId
     * @param string $modName
     * @return array
     * @throws \dml_exception
     */
    public function getFilesForComponentIntro(int $contextId, string $modName)
    {
        $wheres = ["contextid = :contextid", "component = :component", "filearea = :filearea"];

        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);
        $params['filearea'] = 'intro';

        $whereSql = implode(" AND ", $wheres);

        $sql = "SELECT * FROM {files} WHERE {$whereSql}";

        // Fetch the stats data.
        return $this->dbM->get_records_sql($sql, $params);
    }

    /**
     * @param int $itemId
     * @param int $courseContextId
     * @return array
     * @throws \dml_exception
     */
    public function getFilesForSectionSummary(int $itemId, int $courseContextId)
    {
        $wheres = [
            "itemid = :itemid",
            "component = :component",
            "filearea = :filearea",
            "contextid = :contextid"
        ];

        $params['itemid'] = $itemId;
        $params['component'] = 'course';
        $params['filearea'] = 'section';
        $params['contextid'] = $courseContextId;

        $whereSql = implode(" AND ", $wheres);

        $sql = "SELECT * FROM {files} WHERE {$whereSql}";

        // Fetch the stats data.
        return $this->dbM->get_records_sql($sql, $params);
    }

    /**
     * @param int $userId
     * @param int $courseContextId
     * @param int $itemId
     * @return array
     * @throws \dml_exception
     */
    public function getFilesOfUserForSectionSummary(int $userId, int $courseContextId, int $itemId)
    {
        $wheres = [
            "itemid = :itemid",
            "component = :component",
            "filearea = :filearea",
            "userid = :userid",
            "contextid = :contextid"
        ];

        $params['itemid'] = $itemId;
        $params['component'] = 'course';
        $params['filearea'] = 'section';
        $params['userid'] = $userId;
        $params['contextid'] = $courseContextId;

        $whereSql = implode(" AND ", $wheres);

        $sql = "SELECT * FROM {files} WHERE {$whereSql}";

        // Fetch the stats data.
        return $this->dbM->get_records_sql($sql, $params);
    }
}
