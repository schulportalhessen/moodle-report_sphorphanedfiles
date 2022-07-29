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

/**
 * Defines the APIs used by sphorphanedfiles reports
 *
 * @package    report_sphorphanedfiles
 * @copyright  2022 Schulportal Hessen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_sphorphanedfiles\Handler;

use report_sphorphanedfiles\Manager;
use InvalidArgumentException;

defined('MOODLE_INTERNAL') || die();

/**
 * If we have time chain of responsibility
 *
 * Class Factory
 */
class Factory
{
    /**
     * @var null
     */
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
     * Handler to handle module label
     * @return LabelHandler
     */
    public function labelHandler(): LabelHandler
    {
        return new LabelHandler($this->apiM);
    }

    /**
     * Handler to handle the summarys of sections
     * @return SectionSummaryHandler
     */
    public function sectionSummaryHandler(): SectionSummaryHandler
    {
        return new SectionSummaryHandler($this->apiM);
    }

    /**
     * This is a common handler for intro of material and activity e.g. choice, assign ...
     * @return IntroHandler
     */
    public function introHandler(): IntroHandler
    {
        return new IntroHandler($this->apiM);
    }

    /**
     * Handler to handle module page
     * @return PageHandler
     */
    public function pageHandler(): PageHandler
    {
        return new PageHandler($this->apiM);
    }

    /**
     * Handler to handle modules that ar not activitys but materials/resources
     * @return ResourceHandler
     */
    public function resourceHandler(): ResourceHandler
    {
        return new ResourceHandler($this->apiM);
    }

    /**
     * get array with implemented and usable handlers
     * @return array|null
     */
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

    /**
     * Checks id handler for an module exists
     * @param $instance
     * @return bool
     */
    public function hasHandlerFor($instance): bool
    {
        foreach ($this->getHandler() as $handler)
            if ($handler->canHandle($instance->modname)) {
                return true;
            }

        return false;
    }

    /**
     * get the responsible handler for a module
     * @param $instance
     * @return Handler
     */
    public function getHandlerFor($instance): Handler
    {
        foreach ($this->getHandler() as $handler)
            if ($handler->canHandle($instance->modname)) {
                return $handler;
            }

        throw new InvalidArgumentException();
    }
}
