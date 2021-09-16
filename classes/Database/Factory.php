<?php

namespace report_sphorphanedfiles\Database;

use moodle_database;

/**
 * Class Factory
 */
class Factory
{
    /**
     * @var moodle_database
     */
    private $dbM;

    /**
     * Factory constructor.
     * @param moodle_database $dbM
     */
    public function __construct(moodle_database $dbM)
    {
        $this->dbM = $dbM;
    }

    public function dataFiles(): DataFiles
    {
        return new DataFiles($this->dbM);
    }

    /**
     * @return moodle_database
     */
    public function getDbM(): moodle_database
    {
        return $this->dbM;
    }
}
