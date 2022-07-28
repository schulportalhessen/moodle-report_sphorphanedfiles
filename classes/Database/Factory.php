<?php

namespace report_sphorphanedfiles\Database;

use moodle_database;

defined('MOODLE_INTERNAL') || die();

/**
 * This is a straightfoward implementation of the Factory Pattern. The
 * class provides access to objects that are necessary for interaction
 * with the Moodle database via a DataFiles abstraction.
 */
class Factory
{
    /**
     * The database connection used by the factory.
     * 
     * @var moodle_database
     */
    private $dbM;

    /**
     * Creates a new factory using the given database connection to
     * access the necessary information.
     * 
     * @param moodle_database $dbM The database connection to be used.
     */
    public function __construct(moodle_database $dbM)
    {
        $this->dbM = $dbM;
    }

    /**
     * Returns an instance of DataFiles, i.e. an abstraction for accessing
     * relevant database information based on the factory's connection
     * object.
     * 
     * @return DataFiles An abstraction providing a high-level view for the
     *                   file storage information managed by Moodle.
     */
    public function dataFiles(): DataFiles
    {
        return new DataFiles($this->getDbM());
    }

    /**
     * Returns the database connection this factory uses when constructing 
     * DataFiles instances.
     * 
     * @return moodle_database The factory's database connection object.
     */
    public function getDbM(): moodle_database
    {
        return $this->dbM;
    }
}
