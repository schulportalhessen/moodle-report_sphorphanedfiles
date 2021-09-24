<?php

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
    public function __construct(moodle_database $dbM)
    {
        $this->dbM = $dbM;
    }

    /**
     * @return DatabaseFactory
     */
    public function database(): DatabaseFactory
    {
        return new DatabaseFactory($this->dbM);
    }

    /**
     * @return Parser
     */
    public function parser(): Parser
    {
        return new Parser();
    }

    /**
     * @return Files
     */
    public function files(): Files
    {
        return new Files();
    }

    /**
     * @return Security
     */
    public function security(): Security
    {
        return new Security($this->dbM);
    }

    public function handler(): HandlerFactory
    {
        return new HandlerFactory($this);
    }
}
