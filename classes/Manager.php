<?php

namespace report_sphorphanedfiles;

use moodle_database;
use InvalidArgumentException;

use report_sphorphanedfiles\Database\Factory as DatabaseFactory;
use report_sphorphanedfiles\Parser\Parser;
use report_sphorphanedfiles\Files\Files;
use report_sphorphanedfiles\Security\Security;
use report_sphorphanedfiles\Handler\Factory as HandlerFactory;
use report_sphorphanedfiles\Handler\Handler;

defined('MOODLE_INTERNAL') || die();

/**
 * Class manager
 */
class Manager
{
    private static $descriptionHandlerActivities = [
        'assign',
        'bigbluebuttonbn',
        'checklist',
        'choice',
        'customcert',
        'data',
        'lti',
        'ratingallocate',
        'feedback',
        'forum',
        'geogebra',
        'glossary',
        'h5pactivity',
        'hotpot',
        'hvp',
        'lesson',
        'mootyper',
        'mootyper',
        'pdfannotator',
        'quiz',
        'realtimequiz',
        'scorm',
        'survey',
        'wiki',
        'workshop',
    ];

    private static $descriptionHandlerMaterials = [
        'book',
        'folder',
        'imscp',
        'lightboxgallery',
        'url',
        'edusharing',
        'unilabel'
    ];

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

    public function hasHandlerFor($component): bool
    {
        if (in_array($component, ["label", "page", "resource"]))
            return true;

        if (in_array($component, self::$descriptionHandlerActivities))
            return true;

        if (in_array($component, self::$descriptionHandlerMaterials))
            return true;

        return false;
    }

    public function getHandlerFor($component): Handler
    {
        switch ($component) {
            case "label":
                return $this->handler()->labelHandler();
            case "page":
                return $this->handler()->pageHandler();
            case "resource":
                return $this->handler()->resourceHandler();
            default:
                if ($this->hasHandlerFor($component)) {
                    return $this->handler()->introHandler();
                } else {
                    throw new InvalidArgumentException();
                }
        }
    }
}
