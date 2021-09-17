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

    public function getDatabase()
    {
        return $this->dbM;
    }

    protected function createWhereString($elements)
    {
        return array_map(function ($element) {
            return $element . " = :" . $element;
        }, $elements);
    }

    protected function prepareStatement($params)
    {
        $where = implode(" AND ", $this->createWhereString(array_keys($params)));

        return "SELECT * FROM {files} WHERE {$where}";
    }

    protected function performQuery($params)
    {
        return $this->getDatabase()->get_records_sql($this->prepareStatement($params), $params);
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
        $params['userid'] = $userId;
        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);

        return $this->performQuery($params);
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
        $params['userid'] = $userId;
        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);
        $params['filearea'] = 'intro';

        return $this->performQuery($params);
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
        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);

        return $this->performQuery($params);
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
        $params['contextid'] = $contextId;
        $params['component'] = sprintf('mod_%s', $modName);
        $params['filearea'] = 'intro';

        return $this->performQuery($params);
    }

    /**
     * @param int $itemId
     * @param int $courseContextId
     * @return array
     * @throws \dml_exception
     */
    public function getFilesForSectionSummary(int $itemId, int $courseContextId)
    {
        $params['itemid'] = $itemId;
        $params['component'] = 'course';
        $params['filearea'] = 'section';
        $params['contextid'] = $courseContextId;

        return $this->performQuery($params);
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
        $params['itemid'] = $itemId;
        $params['component'] = 'course';
        $params['filearea'] = 'section';
        $params['userid'] = $userId;
        $params['contextid'] = $courseContextId;

        return $this->performQuery($params);
    }
}
