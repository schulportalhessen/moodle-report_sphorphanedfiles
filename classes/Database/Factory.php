<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
