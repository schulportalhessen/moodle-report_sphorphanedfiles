<?php

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Manager;

/**
 * if we have time chain of responsibility
 * Class Factory
 */
class Factory
{
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
}
