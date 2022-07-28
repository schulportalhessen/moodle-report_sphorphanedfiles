<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Manager;
use InvalidArgumentException;

defined('MOODLE_INTERNAL') || die();

/**
 * if we have time chain of responsibility
 * Class Factory
 */
class Factory
{
    private static $handlers = null;

    /**
     * @var Manager
     */
    private $apiM;

    /**
     * Factory constructor.
     * @param Manager $apiM
     */
    public function __construct(Manager $apiM)
    {
        $this->apiM = $apiM;
    }

    /**
     * @return LabelHandler
     */
    public function labelHandler(): LabelHandler
    {
        return new LabelHandler($this->apiM);
    }

    /**
     * @return SectionSummaryHandler
     */
    public function sectionSummaryHandler(): SectionSummaryHandler
    {
        return new SectionSummaryHandler($this->apiM);
    }

    /**
     * this is a common handler for intro of material and activity e.g. choice, assign...
     * @return IntroHandler
     */
    public function introHandler(): IntroHandler
    {
        return new IntroHandler($this->apiM);
    }

    /**
     * @return PageHandler
     */
    public function pageHandler(): PageHandler
    {
        return new PageHandler($this->apiM);
    }

    /**
     * @return ResourceHandler
     */
    public function resourceHandler(): ResourceHandler
    {
        return new ResourceHandler($this->apiM);
    }

    public function getHandler(): array
    {
        if (self::$handlers === null)
            self::$handlers = [
                $this->labelHandler(),
                $this->pageHandler(),
                $this->resourceHandler(),
                $this->sectionSummaryHandler(),
                $this->introHandler()
            ];

        return static::$handlers;
    }

    public function hasHandlerFor($instance): bool
    {
        foreach ($this->getHandler() as $handler)
            if ($handler->canHandle($instance->modname)) {
                return true;
            }

        return false;
    }

    public function getHandlerFor($instance): Handler
    {
        foreach ($this->getHandler() as $handler)
            if ($handler->canHandle($instance->modname)) {
                return $handler;
            }

        throw new InvalidArgumentException();
    }
}
