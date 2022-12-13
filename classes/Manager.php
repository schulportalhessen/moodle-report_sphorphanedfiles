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

namespace report_sphorphanedfiles;

use moodle_database;

use report_sphorphanedfiles\Database\Factory as DatabaseFactory;
use report_sphorphanedfiles\Parser\Parser;
use report_sphorphanedfiles\Files\Files;
use report_sphorphanedfiles\Security\Security;
use report_sphorphanedfiles\Handler\Factory as HandlerFactory;

defined('MOODLE_INTERNAL') || die();

/**
 * Class manager
 */
class Manager
{
    /**
     * @var moodle_database
     */
    private $dbM;

    /**
     * Manager constructor.
     * @param moodle_database $dbM
     */
    public function __construct(moodle_database $dbM) {
        $this->dbM = $dbM;
    }

    /**
     * @return DatabaseFactory
     */
    public function database(): DatabaseFactory {
        return new DatabaseFactory($this->dbM);
    }

    /**
     * @return Parser
     */
    public function parser(): Parser {
        return new Parser();
    }

    /**
     * @return Files
     */
    public function files(): Files {
        return new Files();
    }

    /**
     * @return Security
     */
    public function security(): Security {
        return new Security($this->dbM);
    }

    public function handler(): HandlerFactory {
        return new HandlerFactory($this);
    }
}
